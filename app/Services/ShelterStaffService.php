<?php

namespace App\Services;

use App\Http\Resources\ShelterStaffResource;
use App\Models\ShelterStaff;

class ShelterStaffService
{
    public function getList(array $filters = [])
    {
        $query = ShelterStaff::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['firstname', 'lastname'], 'LIKE', '%' . $filters['search'] . '%');
        }

        $position = $filters['position_id'] ?? null;
        $shelter_position = $filters['shelter_position_id'] ?? null;
        $sort = $filters['sort'] ?? 'desc';
        $shelter_id = $filters['shelter_id'] ?? null;

        if ($shelter_id != null) {
            $query->where('shelter_id', $shelter_id);
        }
        if ($position != null) {
            $query->whereHas('user', function ($q) use ($position) {
                $q->where('position_id', $position);
            });
        }
        if ($shelter_position != null) {
            $query->where('shelter_position_id', $shelter_position);
        }
        if ($sort != null) {
            $query->orderBy('created_at', $sort);
        }
        
        $data = $query->with('user.prefix:id,name', 'user.position:id,name', 'user.department:id,name', 'shelterPosition')->paginate(10);
        return $data->setCollection(ShelterStaffResource::collection($data->items())->collection);
    }

    public function create(array $data)
    {
        $staffs = [];
        foreach ($data['user_id'] as $user_id) {
            $isCreated = ShelterStaff::where('user_id', $user_id)->first();
            if ($isCreated && $isCreated->shelter_id == null) {
                $isCreated->update(['shelter_id' => $data['shelter_id']]);
                $staffs[] = $isCreated;
            } else {
                $staffs[] = ShelterStaff::create([
                    'user_id' => $user_id,
                    'shelter_id' => $data['shelter_id'],
                ]);
            }
        }
        return $staffs;
    }
}
