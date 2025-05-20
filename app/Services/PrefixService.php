<?php

namespace App\Services;

use App\Models\Prefix;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PrefixService
{
    /**
     * Get all news with filter and limit number.
     */
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = Prefix::query();
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
        $query = Prefix::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }
        return $query->latest()->paginate($perPage);
    }

    /**
     * Get a news by its id.
     */
    public function getById(int $id): Prefix
    {
        return Prefix::findOrFail($id);
    }

    /**
     * Create a news.
     */
    public function create(array $data): Prefix
    {
        return Prefix::create($data);
    }

    /**
     * Update a news.
     */
    public function update(Prefix $prefix, array $data): Prefix
    {
        $prefix->update($data);
        return $prefix;
    }

    /**
     * Delete a news.
     */
    public function delete(Prefix $prefix): bool
    {
        return $prefix->delete();
    }
}
