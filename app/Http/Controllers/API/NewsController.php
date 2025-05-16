<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use App\Http\Responses\ApiResponse;
use App\Models\News;
use App\Services\ImageService;
use App\Services\NewsService;

class NewsController extends Controller
{
    protected $newsService, $imageService;

    public function __construct(NewsService $newsService, ImageService $imageService)
    {
        $this->newsService = $newsService;
        $this->imageService = $imageService;
    }

    public function index()
    {
        $news = $this->newsService->getList([], 4);
        return ApiResponse::successResponse(__('success.data'), new NewsCollection($news));
    }

    public function store(NewsRequest $request)
    {
        $data = $this->newsService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, News::IMAGE_FOLDER, News::class, $data->id);
        return ApiResponse::successResponse(__('success.created'), new NewsResource($data));
    }

    public function show(News $news)
    {
        $news->image = $this->imageService->getByTableAndId(News::IMAGE_FOLDER, $news->id);
        return ApiResponse::createdResponse(__('success.created'), new NewsResource($news));
    }

    public function update(NewsRequest $request, News $news)
    {
        $data = $this->newsService->update($news, $request->all());
        $data->image = $this->imageService->updateImage($request->image, News::IMAGE_FOLDER, News::class, $data->id);
        return ApiResponse::successResponse(__('success.updated'), new NewsResource($data));
    }

    public function destroy(News $news)
    {
        $this->newsService->delete($news);
        $this->imageService->deleteImage(News::class, $news->id);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
