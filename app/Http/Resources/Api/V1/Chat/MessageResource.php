<?php

namespace App\Http\Resources\Api\V1\Chat;

use App\Http\Resources\Api\v1\Chat\RoomResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
          'id'          => $this->id,
          'body'        => $this->body,
          'room'        => RoomResource::make($this->whenLoaded('room')),
          'sender'      => MemberResource::make($this->whenLoaded('sender')),
          'created_at'  => $this->created_at,
          'updated_at'  => $this->updated_at,
      ];
    }

    private function getCommentable()
    {
        return match ($this->commentable_type){
            'App\Models\BoardPost' => BoardPostResource::make($this->commentable)
        };
    }
}
