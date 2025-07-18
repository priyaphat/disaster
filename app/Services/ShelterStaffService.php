<?php

namespace App\Services;

use App\Http\Resources\ShelterStaffResource;
use App\Models\ShelterStaff;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShelterStaffService
{
    public function getList(array $filters = [], bool $paginate = true, int $perPage = 10)
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

        $query = $query->with('user.prefix:id,name', 'user.position:id,name', 'user.department:id,name', 'shelterPosition');

        if (!$paginate) {
            return ShelterStaffResource::collection($query->get());
        }

        $data = $query->paginate($perPage);
        return $data->setCollection(ShelterStaffResource::collection($data->items())->collection);
    }

    public function create(array $data)
    {
        return ShelterStaff::create($data);
    }

    /**
     * Update a shelter position.
     */
    public function update(ShelterStaff $staff, array $data): ShelterStaff
    {
        $checkExists = $this->leaderExists($data);
        if ($checkExists) {
            throw new BadRequestHttpException(__('error.staff.leader_exists'));
        }
        $staff->update($data);
        return $staff;
    }

    /**
     * Delete a shelter position.
     */
    public function delete(ShelterStaff $position): bool
    {
        return $position->delete();
    }

    /**
     * Delete a shelter position.
     */
    public function deleteAll($shelterId): bool
    {
        return ShelterStaff::where('shelter_id', $shelterId)->each->delete();
    }


    public function createMany(array $data)
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

    public function leaderExists(array $data = [])
    {
        if ($data['shelter_position_id'] == ShelterStaff::LEADER_ID) {
            return ShelterStaff::where(['shelter_id' => $data['shelter_id'], 'shelter_position_id' => ShelterStaff::LEADER_ID])
                ->where('user_id', '!=', $data['user_id'])->exists();
        }
        return false;
    }
}
