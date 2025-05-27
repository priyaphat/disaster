<?php

namespace App\Services;

use App\Models\Disaster;

class DisasterService
{
    public function index()
    {
        return Disaster::all();
    }

    public function show(Disaster $disaster): Disaster
    {
        return $disaster;
    }

    public function create(array $data): Disaster
    {
        return Disaster::create($data);
    }

    public function update(Disaster $disaster, array $data): Disaster
    {
        $disaster->update($data);
        return $disaster;
    }
}

