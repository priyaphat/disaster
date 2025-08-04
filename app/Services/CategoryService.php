<?php

namespace App\Services;

use App\Models\Category;
use App\Services\SearchService;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    protected SearchService $search;

    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    public function index($filter)
    {
        $query = Category::select('categories.*')
            ->selectSub(function ($q) {
                $q->from('products')
                    ->select('id')
                    ->whereColumn('products.category_id', 'categories.id')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            }, 'used');
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
