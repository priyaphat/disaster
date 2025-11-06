<?php

namespace App\Http\Controllers\API;

use App\Events\EmcRequestNotification;
use App\Events\ProductNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NewsResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\Config;
use App\Models\EmergencyRequest;
use App\Models\News;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Product;
use App\Models\ProductLogs;
use App\Notifications\SendNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->notificationService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead();
        return ApiResponse::successResponse(__('success.data'));
    }

    public function bulkDelete(NotificationRequest $request)
    {
        $this->notificationService->bulkDelte($request->only('notification_id'));
        return ApiResponse::successResponse(__('success.deleted'));
    }

    public function deleteAll()
    {
        $this->notificationService->deleteAll();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    public function emcNotification(Request $request)
    {
        $data = EmergencyRequest::first();
        EmcRequestNotification::dispatch([$data->emergency_team_id]);
        ProductNotification::dispatch();
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    public function notification(Request $request)
    {
        $notification = Notification::first();
        $notification = new NotificationResource($notification);
        $notification = $notification->toArray(request());
        return ApiResponse::successResponse(__('success.data'), $notification);
    }
}
