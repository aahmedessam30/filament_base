<?php

namespace App\Services\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Events\{PrivateChat, PublicChat};
use App\Notifications\FirebaseNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\{User, Chat\Message, Chat\MessageView, Chat\Room};

class Pusher
{
    /**
     * Get Auth User
     *
     * @param string|null $guard
     * @return \Illuminate\Contracts\Auth\Authenticatable|null $user
     */
    private function authUser(string $guard = null)
    {
        return ($guard ? auth($guard) : auth())->user();
    }

    /**
     * Get memberable_id
     *
     * @param mixed $reciver
     * @return integer $memberable_id
     */
    private function memberableId(mixed $reciver): int
    {
        return is_array($reciver) ? $reciver['membrable_id'] : $reciver->id;
    }

    /**
     * Get memberable_type
     *
     * @param mixed $reciver
     * @return string $memberable_type
     */
    private function memberableType(mixed $reciver): string
    {
        return is_array($reciver) ? $reciver['membrable_type'] : $reciver->getMorphClass();
    }

    /**
     * Auth Member Id In Room
     *
     * @param int $room_id
     * @param string $guard
     * @return int|null $member_id
     */
    private function authMemberId(int $room_id, string $guard = 'sanctum'): int|null
    {
        return $this->authUser($guard)->members()->where('room_id', $room_id)->first('id')?->id;
    }
    /**
     * Get Room
     * @param string $type
     * @param mixed $reciver
     * @return \App\Models\Chat\Room|null $room
     */
    private function room(string $type, mixed $reciver): Room|null
    {
        return Room::whereType(strtolower($type))
            ->whereHas('members', fn($q) => $q->where([
                    ['memberable_id', $this->memberableId($reciver)],
                    ['memberable_type', $this->memberableType($reciver)]]
            ))->first();
    }
    /**
     * Get Private Room
     * @param array|Model $reciver
     * @param string $guard
     * @return \App\Models\Chat\Room $room
     */
    public function privateChat(Model|array $reciver, string $guard = 'sanctum'): Room
    {
        if (!($room = $this->room('private', $reciver))) {
            $room = Room::create(['type' => 'private']);
            $room->members()->create(['memberable_id' => $this->authUser($guard)->id, 'memberable_type' => $this->authUser($guard)->getMorphClass()]);
            $room->members()->create(['memberable_id' => $this->memberableId($reciver), 'memberable_type' => $this->memberableType($reciver)]);
        }
        return $room->load('members');
    }

    /**
     * Get Public Room
     * @param array|object $members
     * @param string $guard
     * @param int|null $room_id
     * @return \App\Models\Chat\Room $room
     */
    public function publicChat(array|object $members, int $room_id = null, string $guard = 'sanctum'): Room
    {
        if (!($room = Room::whereType('public')->find($room_id))) {
            $room = Room::create(['type' => 'public']);
            $room->members()->create(['memberable_id' => $this->authUser($guard)->id, 'memberable_type' => $this->authUser($guard)->getMorphClass()]);
            foreach ($members as $member) {
                $room->members()->create(['memberable_id' => $this->memberableId($member), 'memberable_type' => $this->memberableType($member)]);
            }
        }
        return $room->load('members');
    }

    public function createRoom($members){
        return $members instanceof User || (is_array($members) && count($members) === 1)
            ? $this->privateChat($members)
            : $this->publicChat($members);
    }

    public function sendMessage($room_id, $message)
    {
        if ($room = Room::find($room_id)) {
            $message = $room->messages()->create([
                'sender_id' => $this->authMemberId($room_id),
                'body'      => $message,
            ]);
            if ($message && $room->type === 'public') {
                $room->members()
                    ->where('memberable_id', '!=', $this->authMemberId($room_id))
                    ->get()
                    ->each(function ($member) use ($message , $room_id) {
                        $message->views()->create([
                            'member_id'     => $member->id,
                            'is_seen'       => false,
                        ]);
                        $message->views()->where('member_id', $this->authMemberId($room_id))->update(['is_seen' => true]);
                    });
            }
            event($room->type === 'private'
                ? new PrivateChat($message, $room, auth()->user())
                : new PublicChat($message, $room, auth()->user()));

            $this->sendFirebaseNotification($room, $message->body);
        }
    }

    private function sendFirebaseNotification($room, $message)
    {
        $room->members()
            ->with('memberable')
            ->where('memberable_id', '!=', $this->authMemberId($room->id))
            ->get()
            ->chunk(100)
            ->each(function ($members) use ($message) {
                Notification::send($members->pluck('memberable'), new FirebaseNotification([
                    'title' => __('notifications.message_sent_from', ['user' => auth()->user()->full_name]),
                    'body'  => $message
                ]));
            });
    }

    private function readPrivateMessage($room_id): void
    {

        Message::whereRelation('room', 'id', $room_id)
            ->where('sender_id', '!=', $this->authMemberId($room_id))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function readPublicMessage($room_id): void
    {
        Message::whereRelation('room', 'id', $room_id)
            ->where('sender_id', '!=', $this->authMemberId($room_id))
            ->whereNull('read_at')
            ->whereRelation('views', 'is_seen', false)
            ->get()
            ->whenNotEmpty(function ($messages) use ($room_id){
                $messages->each(function ($message) use ($room_id){
                    MessageView::where(['message_id'=> $message->id, 'member_id' => $this->authMemberId($room_id)])->update(['is_seen' => true]);
                    if ($message->unseenCount() === 0) $message->update(['read_at' => now()]);
                });
            });
    }
    public function readMessage($room)
    {
        $room->type === 'private'
            ? $this->readPrivateMessage($room->id)
            : $this->readPublicMessage($room->id);
    }
}
