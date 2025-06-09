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
            'shelter_id' => $this->when($this->relationLoaded('appointment'), $this->appointment?->shelter?->id),
            'team_id' =>  $this->when($this->relationLoaded('appointment'), $this->appointment?->team?->id),
            'shelter_name' => $this->appointment?->shelter_log ? $this->appointment?->shelter_log?->name : $this->appointment?->shelter?->name,
            'team_name' => $this->appointment?->team_log ? $this->appointment?->team_log?->name : $this->appointment?->team?->name,
            'shift_start_time' => $this->shift_start_time,
            'shift_end_time' => $this->shift_end_time
        ];
    }
}
