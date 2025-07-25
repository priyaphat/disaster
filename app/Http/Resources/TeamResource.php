<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this->members, $this->relationLoaded('members'), UserResource::collection($this->members));
        return [
            'id' => $this->id,
            'name' => $this->name,
            'leader' =>  $this->when($this->relationLoaded('leader'), fn() => new MemberResource($this->leader, $this->name)),
            'total_members' => $this->when($this->relationLoaded('allUsers'), fn() => $this->allUsers->count()),
            'all_members' => $this->when($this->relationLoaded('allUsers'), fn() => new MemberCollection($this->allUsers, $this->name)),
            'members' => $this->when($this->relationLoaded('members'), fn() => new MemberCollection($this->members, $this->name)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
