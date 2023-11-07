<?php

namespace App\Http\Resources\Api\V1\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
                'id'            => $this->id,
                'type'          => $this->type,
                'created_at'    => $this->created_at,
                'updated_at'    => $this->updated_at,
                'messages'      => MessageResource::collection($this->messages),
                'members'       => MemberResource::collection($this->members),
            ];
    }
}
