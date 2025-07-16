<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Requests\FilterNewsRequest;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LinePostUtility;
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

    /**
     * News | get news .
     */
    public function index(FilterNewsRequest $request)
    {
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $shelter = $request->has('shelter_id');
        $data = $this->newsService->getList($request->all(), $shelter, $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * News | create news .
     */
    public function store(NewsRequest $request)
    {
        $data = $this->newsService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, News::IMAGE_FOLDER, News::class, $data->id);
        LinePostUtility::line_oa_call(LineController::News_post, $data->toArray());
        return ApiResponse::createdResponse(__('success.new.created'), new NewsResource($data));
    }

    /**
     * News | get news .
     */
    public function show(News $news)
    {
        $news->load(['creator', 'editor', 'image']);
        return ApiResponse::successResponse(__('success.data'), new NewsResource($news));
    }

    /**
     * News | update news .
     */
    public function update(NewsRequest $request, News $news)
    {
        unset($request['shelter_id']);
        $data = $this->newsService->update($news, $request->all());
        $data->image = $this->imageService->updateImage($request->image, News::IMAGE_FOLDER, News::class, $data->id);
        LinePostUtility::line_oa_call(LineController::News_update, $data->toArray());
        return ApiResponse::successResponse(__('success.new.updated'), new NewsResource($data));
    }

    /**
     * News | delete news .
     */
    public function destroy(News $news)
    {
        $this->newsService->delete($news);
        $this->imageService->deleteImage(News::IMAGE_FOLDER, News::class, $news->id);
        return ApiResponse::successResponse(__('success.new.deleted'));
    }
}
