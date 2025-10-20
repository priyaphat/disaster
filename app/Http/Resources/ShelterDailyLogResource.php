<?php

namespace App\Http\Resources;

use App\Services\PopulationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShelterDailyLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $summary = app(PopulationService::class)->summary(['shelter_id' => $this->id]);
        
        return [
            'shelter_id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'max_capacity' => $this->max_capacity,
            'total_victims' => $summary['total_victims'],
            'total_injuries' => $summary['total_injuries'],
            'total_healthy' => $summary['total_healthy'],
            'total_deaths' => $summary['total_deaths'],
        ];
    }
}
