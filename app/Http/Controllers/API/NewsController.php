<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Requests\FilterNewsRequest;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use App\Http\Responses\ApiResponse;
use App\Models\News;
use App\Services\ImageService;
use App\Services\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $newsService, $imageService;

    public function __construct(NewsService $newsService, ImageService $imageService)
    {
        $this->newsService = $newsService;
        $this->imageService = $imageService;
    }

    public function index(FilterNewsRequest $request)
    {
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $shelter = $request->has('shelter_id');
        $data = $this->newsService->getList($request->all(), $shelter, $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(NewsRequest $request)
    {
        $data = $this->newsService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, News::IMAGE_FOLDER, News::class, $data->id);
        return ApiResponse::createdResponse(__('success.new.created'), new NewsResource($data));
    }

    public function show(News $news)
    {
        $news->load(['creator', 'editor', 'image']);
        return ApiResponse::successResponse(__('success.data'), new NewsResource($news));
    }

    public function update(NewsRequest $request, News $news)
    {
        $data = $this->newsService->update($news, $request->all());
        $data->image = $this->imageService->updateImage($request->image, News::IMAGE_FOLDER, News::class, $data->id);
        return ApiResponse::successResponse(__('success.new.updated'), new NewsResource($data));
    }

    public function destroy(News $news)
    {
        $this->newsService->delete($news);
        $this->imageService->deleteImage(News::class, $news->id);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
