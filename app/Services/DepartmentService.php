<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DepartmentService
{
    /**
     * Get all news with filter and limit number.
     */
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = Department::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        // Orders by 'created_at' DESC if ASC use oldest
        $query->latest();

        // limit the number of results
        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }

    /**
     * Get paginated news.
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Department::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }
        return $query->latest()->paginate($perPage);
    }

    /**
     * Get a news by its id.
     */
    public function getById(int $id): Department
    {
        return Department::findOrFail($id);
    }

    /**
     * Create a news.
     */
    public function create(array $data): Department
    {
        return Department::create($data);
    }

    /**
     * Update a news.
     */
    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department;
    }

    /**
     * Delete a news.
     */
    public function delete(Department $department): bool
    {
        return $department->delete();
    }
}
