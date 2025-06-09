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
            'id' => $this['id'],
            'team_name' => $this['team_name'],
            'shelter_name' => $this['shelter_name'],
            'shift_start_time' => $this['shift_start_time'],
            'shift_end_time' => $this['shift_end_time']
        ];
    }
}
