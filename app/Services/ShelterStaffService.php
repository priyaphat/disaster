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

        if ($position != null) {
            $query->whereHas('user', function ($q) use ($position) {
                $q->where('position_id', $position);
            });
        }
        // if ($shelter_position != null) {
        //     $query->whereHas('user', function ($q) use ($position) {
        //         $q->where('position_id', $position);
        //     });
        // }
        if ($sort != null) {
            $query->orderBy('created_at', $sort);
        }
        $data = $query->with('user.prefix:id,name', 'user.position:id,name', 'user.department:id,name')->paginate(10);
        return $data->setCollection(ShelterStaffResource::collection($data->items())->collection);
    }

    public function create(array $data): ShelterStaff
    {
        return ShelterStaff::create($data);
    }
}
