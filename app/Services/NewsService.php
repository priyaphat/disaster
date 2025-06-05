<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NewsService
{
    /**
     * Get all news with filter and limit number.
     */
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = News::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['title', 'description'], 'LIKE', '%' . $filters['search'] . '%');
        }

        // Orders by 'created_at' DESC if ASC use oldest
        $query->latest();

        // limit the number of results
        if ($limit) {
            $query->take($limit);
        }

        return $query->with('image')->get();
    }

    public function get_all($req)
    {
        $data = News::select('*');
        if ($req->search) $data = $data->where('title', $req->search);
        if ($req->dateStart && $req->dateEnd) {
            $start = Carbon::parse($req->dateStart)->startOfDay();
            $end = Carbon::parse($req->dateEnd)->endOfDay();
            $data = $data->whereBetween('created_at', [$start, $end]);
        }
        if ($req->sort) $data = $data->orderBy('created_at', $req->sort === 'asc' ? 'asc' : 'desc');
        if ($req->self) $data = $data->where('created_by', Auth::user()->id);
        return $data->with('image')->get();
    }

    /**
     * Get paginated news.
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = News::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['title', 'description'], 'LIKE', '%' . $filters['search'] . '%');
        }
        return $query->latest()->paginate($perPage);
    }

    /**
     * Get a news by its id.
     */
    public function getById(int $id): News
    {
        return News::findOrFail($id);
    }

    /**
     * Create a news.
     */
    public function create(array $data): News
    {
        return News::create($data);
    }

    /**
     * Update a news.
     */
    public function update(News $news, array $data): News
    {
        $news->update($data);
        return $news;
    }

    /**
     * Delete a news.
     */
    public function delete(News $news): bool
    {
        return $news->delete();
    }
}
