<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Requests\FilterNewsRequest;
use App\Http\Resources\NewsResource;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LinePostUtility;
use App\Models\News;
use App\Models\User;
use App\Services\ImageService;
use App\Services\NewsService;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewsController extends Controller
{
    use AuthorizesRequests;
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
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
        $this->authorize('create', News::class);
        $data = $this->newsService->create($request->all());
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
        $this->authorize('update', $news);
        $data = $this->newsService->update($news, $request->except('shelter_id'));
        return ApiResponse::successResponse(__('success.new.updated'), new NewsResource($data));
    }

    /**
     * News | delete news .
     */
    public function destroy(News $news)
    {
        $this->authorize('delete', $news);
        $this->newsService->delete($news);
        return ApiResponse::successResponse(__('success.new.deleted'));
    }
}
