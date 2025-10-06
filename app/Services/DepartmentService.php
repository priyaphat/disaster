<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DepartmentService
{
    /**
     * Get all department with filter and limit number.
     */
    public function getList(array $filters = [], bool $paginate = false, int $perPage = 10)
    {
        $data = Department::filter($filters)->latest();
        if (!$paginate) {
            return $data->get();
        } else {
            return $data->paginate($perPage);
        }
    }

    /**
     * Get paginated department.
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
     * Get a department by its id.
     */
    public function getById(int $id): Department
    {
        return Department::findOrFail($id);
    }

    /**
     * Create a department.
     */
    public function create(array $data): Department
    {
        return Department::create($data);
    }

    /**
     * Update a department.
     */
    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department;
    }

     /**
     * Check department is used.
     */
    public function isUsed(Department $department): bool
    {
        return $department->users()->exists();
    }

    /**
     * Delete a department.
     */
    public function delete(Department $department): bool
    {
        if($this->isUsed($department)){
            throw new BadRequestHttpException(__('error.sql.fk-constraint'));
        }

        return $department->delete();
    }
}
