<?php

namespace App\Services;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PositionService
{
    /**
     * Get all positions with filter and limit number.
     */
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = Position::query();
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
        return $position->delete();
    }
}
