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
            'shelter_id' => $this->shelter_id,
            'shelter_name' => $this->shelterLog ? $this->shelterLog?->description['name'] : $this->shelter->name,
            'shelter_log_id' => $this->shelterLog ?  $this->shelterLog->id : null,
            'team_id' =>  $this->team_id,
            'team_name' =>  $this->teamLog ? $this->teamLog?->description['name'] : $this->team?->name,
            'team_log_id' => $this->teamLog ?  $this->teamLog->id : null,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'info_disabled' => $this->info_disabled
        ];
    }
}
