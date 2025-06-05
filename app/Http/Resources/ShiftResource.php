<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
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
            'team_id' =>  $this->when($this->relationLoaded('appointment'), $this->appointment?->team?->id),
            'team_name' => $this->when($this->relationLoaded('appointment'), $this->appointment?->team?->name),
            'shelter_id' => $this->when($this->relationLoaded('appointment'), $this->appointment?->shelter?->id),
            'shelter_name' => $this->when($this->relationLoaded('appointment'), $this->appointment?->shelter?->name),
            'shift_start_time' => $this->shift_start_time,
            'shift_end_time' => $this->shift_end_time
        ];
    }
}
