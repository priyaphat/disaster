<?php

namespace App\Services;

use App\Models\Category;
use App\Services\SearchService;

class CategoryService
{
    protected SearchService $search;

    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    public function index($filter)
    {
        $query = Category::query();
        return $this->search->search($query, $filter, 'get');
    }

    public function create(array $data): Category
    {
        $category = Category::create($data);
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }
}
