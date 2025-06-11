<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShelterStaffResource extends JsonResource
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
            'name' => $this->user->prefix->name . ' ' . $this->user->firstname . ' ' . $this->user->lastname,
            'position_id' => $this->user->position_id,
            'position_name' => $this->user->position->name,
            'shelter_position_id' => $this->shelter_position_id,
            'shelter_position_name' => $this->shelter_position ? $this->shelter_position->name : null,
            // 'department_name' => $this->department ? $this->department->name : null,
        ];
    }
}
