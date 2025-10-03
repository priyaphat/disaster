<?php

namespace App\Services;

use App\Models\ShelterPoint;

class ShelterPointService
{
    /**
     * Search name of sheletr point.
     */
    public function search(array $filters = [])
    {
        $search = $filters['search'] ?? null;
        $offset = $filters['offset'] ?? 0;
        $limit = $filters['limit'] ?? 10;

        $data = ShelterPoint::where('name', 'like', "%{$search}%")
        ->orderByRaw("CASE
            WHEN name LIKE ? THEN 1
            ELSE 2
            END, name ASC ", [$search . '%'])
        ->skip($offset)->take($limit)->pluck('name');

        return $data;
    }

    /**
     * Create or update a shelter point.
     */
    public function updateOrCreate(array $data)
    {
        return ShelterPoint::updateOrCreate(
            [
                'external_id' => $data['id']
            ],
            [
                'name' => $data['name'],
                'map_url' => $data['map_url']
            ]
        );
    }
}
