<?php

namespace App\Http\Controllers\Api\V1;

use App\Facades\Chat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Chat\StoreRequestChat;
use App\Http\Requests\Api\v1\Chat\StoreRoomRequest;
use App\Http\Resources\Api\v1\{App\Http\Resources\Api\v1\Shared\ErrorResource,
    App\Http\Resources\Api\v1\Shared\SuccessResource,
    Chat\MessageResource,
    Chat\RoomResource};
use App\Models\{Chat\Message, Chat\Room, User};
use Illuminate\Support\Facades\{DB, Log};

class ChatController extends Controller
{
    public function index()
    {
        $rooms = Room::with([
            'members',
            'messages' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
        ])->paginate(config('app.pagination'));

        return ($rooms->isNotEmpty())
            ? RoomResource::collection($rooms)
            : RoomResource::collection($rooms)->additional(['message' => 'No rooms found']);
    }

    public function show($id)
    {
        $messages = Message::with('sender', 'views')
            ->whereRelation('room', 'room_id', $id)
            ->get();

        if ($room = Room::find($id)) Chat::readMessage($room);

        return ($messages->isNotEmpty())
            ? MessageResource::collection($messages->load('room'))
            : new \App\Http\Resources\Api\Shared\ErrorResource(__('admin.not_found', ['attribute' => __('attributes.room')]));
    }

    public function store(StoreRequestChat $request, $id)
    {
        try {
            DB::beginTransaction();
            Chat::sendMessage($id, $request->body);
            DB::commit();
            return new \App\Http\Resources\Api\Shared\SuccessResource(__('admin.created_successfully', ['attribute' => __('attributes.message')]));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('admin')->error("Error in ChatController@store: " . $e->getMessage());
            return new \App\Http\Resources\Api\Shared\ErrorResource(__('admin.created_failed', ['attribute' => __('attributes.message')]));
        }
    }

    public function storeRoom(StoreRoomRequest $request)
    {
        try {
            if ($members = User::find($request->members)) {
                return ($room = Chat::createRoom($members))
                    ? RoomResource::make($room)->additional(['message' => __('admin.created_successfully', ['attribute' => __('attributes.room')])])
                    : \App\Http\Resources\Api\Shared\ErrorResource::make(__('admin.created_failed', ['attribute' => __('attributes.room')]));
            }
            return \App\Http\Resources\Api\Shared\ErrorResource::make(__('admin.not_found', ['attribute' => __('attributes.members')]));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('admin')->error("Error in ChatController@storeRoom: {$e->getMessage()} at Line: {$e->getLine()} in File: {$e->getFile()}");
            return \App\Http\Resources\Api\Shared\ErrorResource::make(__('admin.created_failed', ['attribute' => __('attributes.room')]));
        }
    }
}
