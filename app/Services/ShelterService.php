<?php

namespace App\Services;

use App\Models\Shelter;

class ShelterService
{
    public function create(array $data): Shelter
    {
        $shelter = Shelter::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'created_date' => $data['created_date'],
            'leader_id' => $data['leader_id'],
            'address' => $data['address'],
            'max_capacity' => $data['max_capacity'],
        ]);

        return $shelter;
    }

    public function update(Shelter $shelter, array $data): Shelter
    {
        $shelter->update($data);
        return $shelter;
    }
}
