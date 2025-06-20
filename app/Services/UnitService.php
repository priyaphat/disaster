<?php

namespace App\Services;

use App\Models\Unit;

class UnitService
{
    public function index(array $filters = [])
    {
        return Unit::all();
    }

    public function create(array $data): Unit
    {
        $unit = Unit::create(['name' => $data['name']]);

        return $unit;
    }

    public function update(Unit $unit, array $data): Unit
    {
        $unit->update($data);
        return $unit;
    }
}
