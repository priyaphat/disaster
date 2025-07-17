<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentLogResource extends JsonResource
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
            'shelter_id' => $this->shelter_id,
            'shelter_name' => $this->shelter?->name,
            'team_id' =>  $this->team_id,
            'team_name' =>  $this->team?->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'team' => $this->team ? new TeamResource($this->team) : null,
            'shelter' => $this->shelter ? new ShelterResource($this->shelter) : null,
        ];
    }
}
