<?php

namespace App\Http\Controllers\API;

use App\Events\Notification as EventsNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Geography\AddressRequest;
use App\Http\Requests\Geography\DistrictRequest;
use App\Http\Requests\Geography\SubDistrictRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\NotificationUtility;
use App\Jobs\BroadcastNotification;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
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

    public function notification(Request $request)
    {
        $user = Auth::user();
        // $user->notify(new SendNotification($data['notification']));
        // $data = Notification::first();
        // Send an notification
        $data = $this->notificationService->createNotification(
            'user.register',
            ['name' => $user->fullname],
            $user->id,
            User::class,
            true
        );
        $userIds = NotificationUtility::getAllUsers();
        BroadcastNotification::dispatch($userIds, $data);
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
