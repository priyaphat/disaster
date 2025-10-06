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
        // dd($id, $data,  $data['status'],  $data['other'], $data['food'], $data['drug']);
        // return DB::table('population_allergies')
        //     ->updateOrInsert(
        //         ['population_id' => $id],
        //         ['status' => $data['status']]
        //     );
        return PopulationAllergy::updateOrCreate(
            ['population_id' => $id],
            ['status' => $data['status']]
        );

        // $record = Allergy::updateOrCreate(
        //     [
        //         'population_id' => 100
        //     ],
        //     [
        //         'population_id' => 17,
        //         'status' => $data['status'],
        //         // 'food' => $data['food'] ?? null,
        //         // 'drug' => $data['drug'] ?? null,
        //         // 'other' => $data['other'] ?? null
        //     ]
        // );

        // dd('Inserted:', $record->toArray(), DB::getQueryLog());
    }
}
