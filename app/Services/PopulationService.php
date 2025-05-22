<?php

namespace App\Services;

use App\Models\Population;
use Illuminate\Container\Attributes\Auth;

class PopulationService
{
    public function create(array $data): Population
    {
        $population = Population::create([
            'prefix_id' => $data['prefix_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'p_address' => $data['p_address'],
            'p_geography_id' => $data['p_geography_id'],
            'c_address' => $data['c_address'],
            'c_geography_id' => $data['c_geography_id'],
            'same_address' => $data['same_address'],
            'status' => $data['status'],
            'shelter_id' => $data['shelter_id'],
            'line_token' => $data['line_token'],
            'created_date' => $data['created_date'],
        ]);

        return $population;
    }

    public function getPopulation(int $id): Population
    {
        return Population::findorFail($id);
    }

    public function update(Population $population, array $data): Population
    {
        $population->update($data);
        return $population;
    }
}
