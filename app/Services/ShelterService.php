<?php

namespace App\Services;

use App\Models\Shelter;
use Illuminate\Database\Eloquent\Collection;

class ShelterService
{
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = Shelter::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['first_name', 'last_name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        $status = $filters['status'] ?? null;

        if ($status != null) {
            $query->where('status', $status);
        }

        // Orders by 'created_at' DESC if ASC use oldest
        $query->latest();

        // limit the number of results
        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }

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
        if ($shelter->wasChanged('status')) {
            //Collcet log shelter status change
        }
        return $shelter;
    }

    public function getShelter($id)
    {
        return Shelter::findOrFail($id);
    }
}
