<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyPost;
use Illuminate\Http\Request;
use App\Services\EmergencyPostService;
use App\Http\Requests\EmergencyPostRequest;

class PostController extends Controller
{
    public function __construct(private EmergencyPostService $emergencyPostService) {}

    /**
     * Post | get post by id / ปุ้ย.
     */
    public function index(EmergencyPost $post)
    {
        $posts = $this->emergencyPostService->index($post);
        return ApiResponse::successResponse(__('success.data'), $posts);
    }

    /**
     * Post | get post ตัวแปร / ปุ้ย.
     */
    public function show(Request $post)
    {
        $posts = $this->emergencyPostService->show($post);
        return ApiResponse::successResponse(__('success.data'), $posts);
    }

    /**
     * Post | สร้าง post ตัวแปร / ปุ้ย.
     */
    public function store(EmergencyPostRequest $request)
    {
        $post = $this->emergencyPostService->create($request->all());
        return ApiResponse::successResponse(__('success.data'), $post);
    }

    /**
     * Post | อับเดต post ตัวแปร / ปุ้ย.
     */
    public function update(EmergencyPostRequest $request, EmergencyPost $post)
    {
        $post = $this->emergencyPostService->update($post, $request->all());
        return ApiResponse::successResponse(__('success.data'), $post);
    }

    /**
     * Post | ลบ post ตัวแปร / ปุ้ย.
     */
    public function destroy(EmergencyPost $post)
    {
        $post = $this->emergencyPostService->delete($post);
        return ApiResponse::successResponse(__('success.data'), $post);
    }

    /**
     * Post | ดึง log post ตัวแปร / ปุ้ย.
     */
    public function logs(Request $request)
    {
        $logs = $this->emergencyPostService->logs($request);
        return ApiResponse::successResponse(__('success.data'), $logs);
    }
}
