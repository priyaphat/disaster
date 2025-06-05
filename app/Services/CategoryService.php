<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function index()
    {
        return Category::all();
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
