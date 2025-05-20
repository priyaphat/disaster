<?php

namespace App\Services;

use App\Models\EmergencyRequest;

class EmergencyRequestService
{

    public function __construct() {}

    public function index()
    {
        $data = EmergencyRequest::all();
        if ($data) {
            $data->map(function ($item) {
                $item->range_age = json_decode($item->range_age);
                $item->victim_name = json_decode($item->victim_name);
            });
        }
        return $data;
    }

    public function show($emc)
    {
        return EmergencyRequest::where('phone', $emc)->first();
    }

    public function create(array $data): EmergencyRequest
    {
        $data['range_age'] = json_encode($data['range_age']);
        $data['victim_name'] = json_encode($data['victim_name']);
        return EmergencyRequest::create($data);
    }

    public function update(EmergencyRequest $emergencyRequest, array $data): EmergencyRequest
    {
        $data['range_age'] = json_encode($data['range_age']);
        $data['victim_name'] = json_encode($data['victim_name']);
        $emergencyRequest->update($data);
        return $emergencyRequest;
    }
}
