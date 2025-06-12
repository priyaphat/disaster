<?php

namespace App\Services;

use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use App\Models\News;

class NewsService
{
    public function getList(array $filters = [], bool $shelter = false, bool $paginate = true, int $perPage = 20)
    {
        if(!$shelter){
            $filters['center'] = true;
        }

        if (!$paginate) {
            $data = News::with(['creator', 'editor', 'image'])->filter($filters)->get();
            return new NewsCollection($data);
        } else {
            $data = News::with(['creator', 'editor', 'image'])->filter($filters)->paginate($perPage);
            $paginate = $data->setCollection(NewsResource::collection($data->items())->collection);
            return  $paginate;
        }
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
