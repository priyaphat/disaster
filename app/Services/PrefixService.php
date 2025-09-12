<?php

namespace App\Services;

use App\Models\Prefix;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PrefixService
{
    /**
     * Get all prefixes with filter and limit number.
     */
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = Prefix::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        // Orders by 'created_at' DESC if ASC use oldest
        $query->oldest();

        // limit the number of results
        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }

    /**
     * Get paginated prefix.
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
     * Get a prefix by its id.
     */
    public function getById(int $id): Prefix
    {
        return Prefix::findOrFail($id);
    }

    /**
     * Create a prefix.
     */
    public function create(array $data): Prefix
    {
        return Prefix::create($data);
    }

    /**
     * Update a prefix.
     */
    public function update(Prefix $prefix, array $data): Prefix
    {
        $prefix->update($data);
        return $prefix;
    }

    /**
     * Delete a prefix.
     */
    public function delete(Prefix $prefix): bool
    {
        return $prefix->delete();
    }
}
