<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $data = $this->categoryService->index();
        return ApiResponse::successResponse(__('success.data'), $data);
    }
    
    public function store(CreateCategoryRequest $request)
    {
        $data = $this->categoryService->create($request->all());
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->categoryService->update($category, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
    
    public function destroy(Category $category)
    {
        $category->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
