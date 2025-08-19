<?php

namespace App\Http\Controllers\API;

use App\Events\Notification as EventsNotification;
use App\Events\Notify;
use App\Http\Controllers\Controller;
use App\Http\Requests\Geography\AddressRequest;
use App\Http\Requests\Geography\DistrictRequest;
use App\Http\Requests\Geography\SubDistrictRequest;
use App\Http\Requests\NotificationRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\NotificationUtility;
use App\Jobs\BroadcastNotification;
use App\Models\Appointment;
use App\Models\DamagedUtility;
use App\Models\EmergencyPost;
use App\Models\EmergencyRequest;
use App\Models\News;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Shelter;
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

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead();
        return ApiResponse::successResponse(__('success.deleted'));
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

    public function notification(Request $request)
    {

        // ศูนย์พักพิงมีนัดหมายใหม่
        // $appointment = Appointment::find(121);
        // $data = [
        //     'shelter' => $appointment->shelter->name,
        //     'date' => $appointment->appointment_date,
        //     'time' => $appointment->shift->value,
        //     'team' => $appointment->team->name,
        // ];
        // $userIds = ['Ua145e851bc8552be58724a47507fbd7b'];
        // (new LineController)->LineNotification($data, LineController::Shelter_appointment, $userIds);


        // ศูนย์พักพิงยกเลิกนัดหมาย
        // $appointment = Appointment::find(121);
        // $data = [
        //     'shelter' => $appointment->shelter->name,
        //     'date' => $appointment->appointment_date,
        //     'time' => $appointment->shift->value,
        //     'team' => $appointment->team->name,
        // ];

        // $userIds = ['Ua145e851bc8552be58724a47507fbd7b'];
        // (new LineController)->LineNotification($data, LineController::Shelter_appointment_canceled, $userIds);

        // ทีม : นัดหมายใหม่
        // $appointment = Appointment::find(121);
        // $data = [
        //     'shelter' => $appointment->shelter->name,
        //     'date' => $appointment->appointment_date,
        //     'time' => $appointment->shift->value,
        //     'team' => $appointment->team->name,
        // ];

        // $userIds = ['Ua145e851bc8552be58724a47507fbd7b'];
        // (new LineController)->LineNotification($data, LineController::Team_appointment, $userIds);

        // ทีม : ยกเลิกนัดหมาย
        // $appointment = Appointment::find(121);
        // $data = [
        //     'shelter' => $appointment->shelter->name,
        //     'date' => $appointment->appointment_date,
        //     'time' => $appointment->shift->value,
        //     'team' => $appointment->team->name,
        // ];

        // $userIds = ['Ua145e851bc8552be58724a47507fbd7b'];
        // (new LineController)->LineNotification($data, LineController::Team_appointment_canceled, $userIds);

        // $notification = Notification::first();
        // event(new EventsNotification('test', Auth::user()->id));
        // ลบ Appointment
        // $user->notify(new SendNotification($data['notification']));
        // $data = Notification::first();

        // Register notification
        // $data = Auth::user();

        // Create a news notification
        // $data = News::first();
        // $this->notificationService->sendToSelfUser($notification, Auth::user()->id);


        // // Update a news notification
        // $data = News::first();
        // $notification = $this->notificationService->createNotification(
        //     'news.updated',
        //     ['title' => $data->title],
        //     $data->id,
        //     User::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);


        // Create a news notification
        // $data = News::first();
        // $notification = $this->notificationService->createNotification(
        //     'shelter_news.created',
        //     ['title' => $data->title],
        //     $data->id,
        //     User::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // // Update a news notification
        // $data = News::first();
        // $notification = $this->notificationService->createNotification(
        //     'shelter_news.updated',
        //     ['title' => $data->title],
        //     $data->id,
        //     User::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // Active shelter
        // $data = Shelter::first();
        // $notification = $this->notificationService->createNotification(
        //     'shelter.opened',
        //     ['name' => $data->name],
        //     $data->id,
        //     Shelter::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // Inactive shelter
        // $data = Shelter::first();
        // $notification = $this->notificationService->createNotification(
        //     'shelter.closed',
        //     ['name' => $data->name],
        //     $data->id,
        //     Shelter::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // Create an appointment
        // $data = Appointment::latest()->first();
        // $data->load('team');


        // Delete an appointment
        // $data = Appointment::latest()->first();
        // $data->load('team');
        // $notification = $this->notificationService->createNotification(
        //     'appointment.deleted',
        //     [
        //         'appointment_date' => $data->appointment_date->thaidate('j M Y'),
        //         'shift' => AppointmentUtility::getShiftName($data->shift),
        //         'team' => $data->team->name
        //     ],
        //     $data->id,
        //     Appointment::class,
        //     true
        // );
        // $this->notificationService->sendToUser($notification);

        // Update an appointment
        // $old = Appointment::latest()->first();
        // $old->load('team');

        // $data = Appointment::latest()->first();
        // $data->load('team');

        // $notification = $this->notificationService->createNotification(
        //     'appointment.updated',
        //     [
        //         'old_date' => $old->appointment_date->thaidate('j M Y'),
        //         'old_shift' => AppointmentUtility::getShiftName($old->shift),
        //         'old_team' => $old->team->name,
        //         'new_date' => $data->appointment_date->thaidate('j M Y'),
        //         'new_shift' => AppointmentUtility::getShiftName($data->shift),
        //         'new_team' => $data->team->name
        //     ],
        //     $data->id,
        //     Appointment::class,
        //     true
        // );
        // $this->notificationService->sendToUser($notification);

        // join team notification
        // $data = TeamMember::latest()->first();
        // $data->load('team');




        // Leave team notification
        // $data = TeamMember::latest()->first();
        // $data->load('team');

        // $notification = $this->notificationService->createNotification(
        //     'team.leave',
        //     [
        //         'name' => $data->team->name,
        //     ],
        //     $data->id,
        //     TeamMember::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);


        // Update team leader notification
        // $data = Team::latest()->first();
        // $data->load('leader');


        // $data = ShelterStaff::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'shelter_staff.tranfer',
        //     [],
        //     $data->id,
        //     ShelterStaff::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // $data = ShelterStaff::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'shelter_staff.leave',
        //     [],
        //     $data->id,
        //     ShelterStaff::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // emergency request
        // $data = EmergencyRequest::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'emergency_request.created',
        //     [
        //         'name' => $data->prefix?->name.' '.$data->first_name.' '.$data->last_name
        //     ],
        //     $data->id,
        //     EmergencyPost::class,
        //     false
        // );
        // $this->notificationService->sendToCenterStaff($notification);


        // $data = EmergencyRequest::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'emergency_request.created',
        //     [
        //         'name' => $data->prefix?->name . ' ' . $data->first_name . ' ' . $data->last_name
        //     ],
        //     $data->id,
        //     EmergencyPost::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // Update position
        // $data = ShelterStaff::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'staff_position.updated',
        //     [],
        //     $data->id,
        //     ShelterStaff::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);


        // Update leader dashboard
        // $data = ShelterStaff::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'staff_leader.updated',
        //     [],
        //     $data->id,
        //     ShelterStaff::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // Emergency post
        // $data = EmergencyPost::latest()->first();
        // $area = $data->areas->pluck('district_name')->toArray();
        // $area = implode(', ', $area);
        // $notification = $this->notificationService->createNotification(
        //     'post.created',
        //     [
        //         'disaster' => 'ดินถล่ม',
        //         'level' => '4',
        //         'area' => '1'
        //     ],
        //     $data->id,
        //     EmergencyPost::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);


        // $data = EmergencyPost::latest()->first();
        // // $area = implode(", ", ['แม่จัน','แม่สาย']);
        // $area = $data->areas->pluck('district_name')->toArray();
        // $area = implode(', ', $area);
        // $notification = $this->notificationService->createNotification(
        //     'post.updated',
        //     [
        //         'disaster' => $data->disaster->name,
        //         'level' => $data->level,
        //         'area' => $area
        //     ],
        //     $data->id,
        //     EmergencyPost::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // $data = EmergencyPost::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'post.deleted',
        //     [
        //         'disaster' => 'ดินถล่ม'
        //     ],
        //     $data->id,
        //     EmergencyPost::class,
        //     false
        // );
        // $this->notificationService->sendToUser($notification);

        // $user = Auth::user();
        // $user->notify(new SendNotification($data['notification']));
        // $data = Notification::first();

        // Send an damage notification
        // $data = DamagedUtility::where('type', 2)->first();
        // $total = DamagedUtility::where('type', 2)->count();
        // $notification = $this->notificationService->createNotification(
        //     'damaged.updated',
        //     [
        //         'name' => $data->type->label(),
        //         'total' => $total
        //     ],
        //     $data->id,
        //     DamagedUtility::class,
        //     true
        // );

        // // $data = EmergencyRequest::latest()->first();
        // $notification = $this->notificationService->createNotification(
        //     'emergency_request.failed',
        //     [
        //         'name' => $data->prefix?->name.' '.$data->first_name.' '.$data->last_name
        //     ],
        //     $data->id,
        //     EmergencyRequest::class,
        //     false
        // );


        // $notification = $this->notificationService->createNotification(
        //     'emergency_request.assign',
        //     [
        //         'name' => $data->prefix?->name.' '.$data->first_name.' '.$data->last_name
        //     ],
        //     $data->id,
        //     EmergencyRequest::class,
        //     false
        // );


        //  $notification = $this->notificationService->createNotification(
        //     'emergency_request.cancel',
        //     [
        //         'name' => $data->prefix?->name.' '.$data->first_name.' '.$data->last_name
        //     ],
        //     $data->id,
        //     EmergencyRequest::class,
        //     false
        // );

        // $notification = $this->notificationService->createNotification(
        //     'emergency_request.accepted',
        //     [
        //         'name' => $data->prefix?->name.' '.$data->first_name.' '.$data->last_name
        //     ],
        //     $data->id,
        //     EmergencyRequest::class,
        //     false
        // );

        // return ApiResponse::successResponse(__('success.data'), $notification);
    }
}
