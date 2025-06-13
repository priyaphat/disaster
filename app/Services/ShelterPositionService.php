<?php

namespace App\Services;

use App\Models\ShelterPosition;

class ShelterPositionService
{
    /**
     * Get all positions.
     */
    public function getList()
    {
        return ShelterPosition::all()->select('id', 'name');
    }

    /**
     * Create a shelter position.
     */
    public function create(array $data): ShelterPosition
    {
        return ShelterPosition::create($data);
    }

    /**
     * Update a shelter position.
     */
    public function update(ShelterPosition $position, array $data): ShelterPosition
    {
        $position->update($data);
        return $position;
    }

    /**
     * Delete a shelter position.
     */
    public function delete(ShelterPosition $position): bool
    {
        return $position->delete();
    }
}
