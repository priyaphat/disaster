<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'shift' => $this->shift,
            'shelter_id' => $this->when($this->relationLoaded('shelter'), $this->shelter?->id),
            'shelter_name' => $this->when($this->relationLoaded('shelter'), $this->shelter?->name),
            'team_id' =>  $this->when($this->relationLoaded('team'), $this->team?->id),
            'team_name' =>  $this->when($this->relationLoaded('team'), $this->team?->name),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
    }
}
