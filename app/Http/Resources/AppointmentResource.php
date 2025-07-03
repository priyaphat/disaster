<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'appointment_date' => Carbon::parse($this->appointment_date)->format('Y-m-d'),
            'shift' => $this->shift,
            'shelter_id' => $this->when($this->relationLoaded('shelter'), $this->shelter?->id),
            'shelter_name' => $this->when($this->relationLoaded('shelter'), $this->shelter?->name),
            'team_id' =>  $this->team_id,
            'team_name' =>  $this->team ? $this->team->name : $this->teamLog?->description['name'],
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'time_disabled' => $this->time_disabled
        ];
    }
}
