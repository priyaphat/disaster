<?php

namespace App\Services;

use App\Models\PopulationAllergy;
use Illuminate\Support\Facades\DB;

class PopulationAllergyService
{
    /**
     * Create a member.
     */
    public function updateOrCreate(int $id, array $data)
    {
        return PopulationAllergy::updateOrCreate(
            ['population_id' => $id],
            [
                'population_id' => $id,
                'status' => $data['status'],
                'food' => $data['food'] ?? null,
                'drug' => $data['drug'] ?? null,
                'other' => $data['other'] ?? null
            ]
        );
    }
}
