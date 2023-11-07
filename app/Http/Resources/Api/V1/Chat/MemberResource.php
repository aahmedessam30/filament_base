<?php

namespace App\Http\Resources\Api\V1\Chat;

use App\Http\Resources\Api\v1\Student\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'id'         => $this->id,
            'user'       => UserResource::make($this->memberable),
            'room'       => RoomResource::make($this->whenLoaded('room')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}
