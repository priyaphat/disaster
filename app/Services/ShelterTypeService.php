<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Shelter;
use App\Models\ShelterType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShelterTypeService
{
    /**
     * Get all sheletr types with filter and limit number.
     */
    public function search(array $filters = [])
    {
        $search = $filters['search'] ?? null;
        $offset = $filters['offset'] ?? 0;
        $limit = $filters['limit'] ?? 10;

        // Search current address in populations
        $data = Shelter::select('shelter_type')->where('shelter_type', 'like', "%{$search}%")
            ->orderByRaw("CASE
            WHEN shelter_type LIKE ? THEN 1
            ELSE 2
            END, shelter_type ASC ", [$search . '%'])->distinct()->skip($offset)->take($limit)->pluck('shelter_type');
        return $data;
    }

    // /**
    //  * Get all department with filter and limit number.
    //  */
    // public function getList(bool $paginate = true, int $perPage = 10)
    // {
    //     $data = ShelterType::select('id', 'name')->latest();
    //     if (!$paginate) {
    //         return $data->get();
    //     } else {
    //         return $data->paginate($perPage);
    //     }
    // }

    // /**
    //  * Create a department.
    //  */
    // public function create(array $data): ShelterType
    // {
    //     return ShelterType::create($data);
    // }

    // /**
    //  * Update a department.
    //  */
    // public function update(ShelterType $type, array $data): ShelterType
    // {
    //     $type->update($data);
    //     return $type;
    // }

    //  /**
    //  * Check department is used.
    //  */
    // public function isUsed(ShelterType $type): bool
    // {
    //     return $type->shelters()->exists();
    // }

    // /**
    //  * Delete a department.
    //  */
    // public function delete(ShelterType $type): bool
    // {
    //     if($this->isUsed($type)){
    //         throw new BadRequestHttpException(__('error.sql.fk-constraint'));
    //     }

    //     return $type->delete();
    // }
}
