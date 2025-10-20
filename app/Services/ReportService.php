<?php

namespace App\Services;

use App\Models\PopulationDailyLog;
use App\Models\ShelterDailyLog;
use App\Models\ShelterDailyReport;
use App\Models\ShelterDailyTotal;

class ReportService
{
    public function updateShelter(array $data)
    {
        return ShelterDailyLog::updateOrCreate(
            [
                'log_date' => $data['date'],
                'shelter_id' => $data['shelter_id']
            ],
            [
                'shelter_id' => $data['shelter_id'],
                'status' => $data['status'],
                'max_capacity' => $data['max_capacity'],
                'total_victims' => $data['total_victims'],
                'total_injuries' => $data['total_injuries'],
                'total_healthy' => $data['total_healthy'],
                'total_deaths' => $data['total_deaths']
            ]
        );
    }

    public function updatePopulation(array $data)
    {
        return PopulationDailyLog::updateOrCreate(
            [
                'log_date' => $data['date'],
                'shelter_id' => $data['shelter_id']
            ],
            [
                'shelter_id' => $data['shelter_id'],
                'status' => $data['status'],
                'max_capacity' => $data['max_capacity'],
                'total_victims' => $data['total_victims'],
                'total_injuries' => $data['total_injuries'],
                'total_healthy' => $data['total_healthy'],
                'total_deaths' => $data['total_deaths']
            ]
        );
    }

    public function updateDashboard($date)
    {
        $data = app(ShelterService::class)->dashboard([]);
        
        return ShelterDailyTotal::updateOrCreate(
            [
                'log_date' => $date,
            ],
            [
                'total_shelter' => $data['total_shelter'],
                'total_victims' => $data['total_victims'],
                'total_injuries' => $data['total_injuries'],
                'total_healthy' => $data['total_healthy'],
                'total_deaths' => $data['total_deaths']
            ]
        );
    }
}
