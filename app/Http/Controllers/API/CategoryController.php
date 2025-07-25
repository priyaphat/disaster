<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Spatie\FlareClient\Api;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $filter)
    {
        $data = $this->categoryService->index($filter);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(CreateCategoryRequest $request)
    {
        $data = $this->categoryService->create($request->all());
        return ApiResponse::createdResponse(__('success.categories.created'), $data);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $this->categoryService->update($category, $request->all());
        return ApiResponse::successResponse(__('success.categories.updated'), $data);
    }

    public function destroy(Category $category)
    {
        if (Product::where('category_id', $category->id)->first()) {
            throw new BadRequestHttpException(__('error.sql.fk-constraint'));
        }
        $category->delete();
        return ApiResponse::successResponse(__('success.categories.deleted'));
    }
}
