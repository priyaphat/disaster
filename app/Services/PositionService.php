<?php

namespace App\Services;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PositionService
{
    /**
     * Get all positions with filter and limit number.
     */
    public function getList(array $filters = [], bool $paginate = true, int $perPage = 10)
    {
        $data = Position::filter($filters)->latest();
        if (!$paginate) {
            return $data->get();
        } else {
            return $data->paginate($perPage);
        }
    }

    /**
     * Get paginated position.
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Position::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }
        return $query->latest()->paginate($perPage);
    }

    /**
     * Get a position by its id.
     */
    public function getById(int $id): Position
    {
        return Position::findOrFail($id);
    }

    /**
     * Create a position.
     */
    public function create(array $data): Position
    {
        return Position::create($data);
    }

    /**
     * Update a position.
     */
    public function update(Position $position, array $data): Position
    {
        $position->update($data);
        return $position;
    }

    /**
     * Delete a position.
     */
    public function delete(Position $position): bool
    {
        if ($position->users) {
            throw new BadRequestHttpException(__('error.sql.fk-constraint'));
        }
    
        return $position->delete();
    }
}
