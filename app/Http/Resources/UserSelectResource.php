<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prefix_id' => $this->prefix_id,
            'prefix_name' => $this->when($this->relationLoaded('prefix'), $this->prefix?->name),
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'fullname' => $this->when($this->relationLoaded('prefix'), $this->prefix?->name.' '.$this->firstname.' '.$this->lastname),
        ];
    }
}
