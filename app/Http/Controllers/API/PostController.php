<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyPost;
use Illuminate\Http\Request;
use App\Services\EmergencyPostService;
use App\Http\Requests\EmergencyPostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\FlareClient\Api;

class PostController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private EmergencyPostService $emergencyPostService) {}

    /**
     * Post | get post by id / ปุ้ย.
     */
    public function index(EmergencyPost $post)
    {
        $this->authorize('viewAny', EmergencyPost::class);
        $posts = $this->emergencyPostService->index($post);
        return ApiResponse::successResponse(__('success.data'), $posts);
    }

    /**
     * Post | get post ตัวแปร / ปุ้ย.
     */
    public function show(Request $post)
    {
        $this->authorize('view', EmergencyPost::class);
        $posts = $this->emergencyPostService->show($post);
        return ApiResponse::successResponse(__('success.data'), $posts);
    }

    /**
     * Post | สร้าง post ตัวแปร / ปุ้ย.
     */
    public function store(EmergencyPostRequest $request)
    {
        $this->authorize('create', EmergencyPost::class);
        $request['status'] = 1; //set default
        $post = $this->emergencyPostService->create($request->all());
        return ApiResponse::successResponse(__('success.post.created'), $post);
    }

    /**
     * Post | อับเดต post ตัวแปร / ปุ้ย.
     */
    public function update(EmergencyPostRequest $request, EmergencyPost $post)
    {
        $this->authorize('update', EmergencyPost::class);
        $post = $this->emergencyPostService->update($post, $request->all());
        if ($post['status'] == EmergencyPost::inactive) return ApiResponse::successResponse(__('success.post.deleted'), $post);
        return ApiResponse::successResponse(__('success.post.updated'), $post);
    }

    /**
     * Post | ลบ post ตัวแปร / ปุ้ย.
     */
    public function destroy(EmergencyPost $post)
    {
        $this->authorize('delete', EmergencyPost::class);
        $post = $this->emergencyPostService->delete($post);
        return ApiResponse::successResponse(__('success.data'), $post);
    }

    /**
     * Post | ดึง log post ตัวแปร / ปุ้ย.
     */
    public function logs(Request $request)
    {
        $this->authorize('viewAny', EmergencyPost::class);
        $logs = $this->emergencyPostService->logs($request);
        return ApiResponse::successResponse(__('success.data'), $logs);
    }
}
