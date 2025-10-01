<?php

namespace App\Http\Controllers\API;

use App\Enums\EmergencyLevel;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyPost;
use Illuminate\Http\Request;
use App\Services\EmergencyPostService;
use App\Http\Requests\EmergencyPostRequest;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\FlareClient\Api;

class PostController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private EmergencyPostService $emergencyPostService, private NotificationService $notificationService) {}

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
        $this->authorize('create', EmergencyPost::class);
        $request['status'] = 1; //set default
        //repetitive updated
        $q = EmergencyPost::where('disaster_id', $request->disaster_id)
            ->where('status', EmergencyPost::active)
            ->where('level', $request->level)
            ->where('district_code', $request->district_code)
            ->first();
        if ($q) return self::update($request, $q);
        //end

        $post = $this->emergencyPostService->create($request->all());

        // Send post notification via socket
        $notification = $this->notificationService->createNotification('post.created', ['disaster' => $post->disaster?->name, 'level' => $post->level, 'area' => $post->gis_map], $post->id, EmergencyPost::class, false);
        $this->notificationService->sendToAllUsers($notification);
        return ApiResponse::successResponse(__('success.post.created'), $post);
    }

    /**
     * Post | อับเดต post ตัวแปร / ปุ้ย.
     */
    public function update(EmergencyPostRequest $request, EmergencyPost $post)
    {
        $this->authorize('update', EmergencyPost::class);
        //repetitive to stop
        $q = EmergencyPost::where('disaster_id', $request->disaster_id)
            ->where('status', EmergencyPost::active)
            ->where('level', $request->level)
            ->where('id', '!=', $post->id)
            ->where('district_code', $request->district_code)
            ->first();
        if ($q) $request['status'] = EmergencyPost::inactive;
        //end

        $post = $this->emergencyPostService->update($post, $request->all());
        // Send post notification via socket
        if ($post['status'] == EmergencyPost::inactive) {
            $notification = $this->notificationService->createNotification('post.deleted', ['disaster' => $post->disaster?->name, 'level' => $post->level, 'area' => $post->gis_map], $post->id, EmergencyPost::class, false);
            $this->notificationService->sendToAllUsers($notification);
            return ApiResponse::successResponse(__('success.post.deleted'), $post);
        }

        $notification = $this->notificationService->createNotification('post.updated', ['disaster' => $post->disaster?->name, 'level' => $post->level, 'area' => $post->gis_map], $post->id, EmergencyPost::class, false);
        $this->notificationService->sendToAllUsers($notification);

        return ApiResponse::successResponse(__('success.post.updated'), $post);
    }

    /**
     * Post | ลบ post ตัวแปร / ปุ้ย.
     */
    public function destroy(EmergencyPost $post)
    {
        $this->authorize('delete', EmergencyPost::class);
        $post = $this->emergencyPostService->delete($post);
        // Send post notification via socket
        $notification = $this->notificationService->createNotification('post.deleted', ['disaster' => $post->disaster?->name, 'level' => $post->level, 'area' => $post->gis_map], $post->id, EmergencyPost::class, false);
        $this->notificationService->sendToAllUsers($notification);
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

    /**
     * Post | post level
     */
    public function level()
    {
        $data = array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], EmergencyLevel::cases());
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
