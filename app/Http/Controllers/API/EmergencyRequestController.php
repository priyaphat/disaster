<?php

namespace App\Http\Controllers\API;

use App\Enums\PopulationStatus;
use App\Events\EmcRequestNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyRequest;
use App\Services\EmergencyRequestService;
use App\Http\Requests\EmergencyRequestRequest;
use App\Http\Utility\LinePostUtility;
use App\Http\Utility\RecipintUtility;
use App\Models\EmergencyRequestLog;
use App\Models\Population;
use App\Models\Prefix;
use App\Models\Role;
use App\Services\NotificationService;
use App\Services\PopulationService;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EmergencyRequestController extends Controller
{
    use AuthorizesRequests;
    protected $emergencyRequestService, $notificationService;

    public function __construct(EmergencyRequestService $emergencyRequestService, NotificationService $notificationService)
    {
        $this->emergencyRequestService = $emergencyRequestService;
        $this->notificationService = $notificationService;
    }

    /**
     * Emergency request | ดึงข้อมูลทุกตัว / ปุ้ย .
     */
    public function index()
    {
        $this->authorize('viewAny', EmergencyRequest::class);
        $emergencyRequest = $this->emergencyRequestService->index();
        return ApiResponse::successResponse(__('success.data'), $emergencyRequest);
    }

    /**
     * Emergency request | ดึงข้อมูล อนุมัติ / ปุ้ย .
     */
    public function show(Request $emergencyRequest)
    {
        // approve
        $this->authorize('viewAny', EmergencyRequest::class);
        $data = $this->emergencyRequestService->show($emergencyRequest, null);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึงข้อมูล รอ , ช่วยเหลือ ตัวแปร / ปุ้ย .
     */
    public function show_wait(Request $emergencyRequest)
    {
        // waiting , helping
        $this->authorize('viewAny', EmergencyRequest::class);
        $data = $this->emergencyRequestService->show($emergencyRequest, 'waiting');
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึงข้อมูล เสร็จ , ไม่เสร็จ , ไม่อนุมัติ  ตัวแปร / ปุ้ย .
     */
    public function show_history(Request $emergencyRequest)
    {
        // complete , disapprove , incomplete
        $this->authorize('viewAny', EmergencyRequest::class);
        $data = $this->emergencyRequestService->show($emergencyRequest, 'history');
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึงข้อมูล id , phone  .
     */
    #[PathParameter('emc', description: 'Phone or id | เบอร์โทรหรือ id', type: 'string', example: '0987654321')]
    public function emcRequestBy(Request $request, $emc = null, $id = null)
    {
        $data = $this->emergencyRequestService->emcRequestBy($request, $emc, $id); //phone & id
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึงข้อมูล id
     */
    #[PathParameter('emc', description: 'Phone or id | เบอร์โทรหรือ id', type: 'string', example: '0987654321')]
    public function emcRequestId(Request $request, $id)
    {
        $data = $this->emergencyRequestService->emcRequestBy($request, null, $id); //phone & id
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | สร้างรายการแจ้งเหตุฉุกเฉิน  ตัวแปร / ปุ้ย .
     */
    public function store(EmergencyRequestRequest $request)
    {
        $data = $this->emergencyRequestService->create($request->all());
        $name = ['name' => Prefix::where('id', $data['prefix_id'])->value('name') . ' ' . $data['first_name'] . ' ' . $data['last_name']];
        $recipint = RecipintUtility::recipint('admin');
        (new LineController)->LineNotification($name, LineController::Emc_request_post, $recipint);
        // Send a emergency request notfication
        $notification = $this->notificationService->createNotification('emergency_request.created', ['name' => $name['name']], $data->id, EmergencyRequest::class, true);
        $this->notificationService->sendToAdmin($notification);
        EmcRequestNotification::dispatch([$data->emergency_team_id]);
        return ApiResponse::createdResponse(__('success.emc_request.created'), $data);
    }

    /**
     * Emergency request | อัพเดตรายการแจ้งเหตุฉุกเฉิน  ตัวแปร / ปุ้ย .
     */
    public function update(EmergencyRequestRequest $request, EmergencyRequest $emergencyRequest)
    {
        $this->authorize('update', $emergencyRequest);

        if ($edit = self::canEdit($emergencyRequest)) return $edit;

        $old = clone $emergencyRequest;
        $skip = false;
        $name = Prefix::where('id', $emergencyRequest['prefix_id'])->value('name') . ' ' . $emergencyRequest['first_name'] . ' ' . $emergencyRequest['last_name'];

        // ต้องดูสถานะด้วยว่าเป็น helping complete incomplete เปลี่ยนไม่ได้แล้ว
        if ($old['emergency_team_id'] && !$request['emergency_team_id']) { //ส่วนกลาง กดลบทีม
            //team = null
            if ($res = self::canCheck($request)) return $res;
            $request['status'] = EmergencyRequest::waiting;
            $skip = true;
            LinePostUtility::LineMakeInformation(LineController::Emc_request_end, $emergencyRequest);
            // หัวหน้าทีมยังไม่กดอนุมัติ

            // ส่งให้แค่หัวหน้าทีม
            $notification = $this->notificationService->createNotification('emergency_request.canceled', ['name' => $name], $emergencyRequest->id, EmergencyRequest::class, false);
            $this->notificationService->sendToTeamLeader($notification, $emergencyRequest->emergency_team_id);
            $textResponse = __('success.emc_request.delTeam2');
        } else if (
            $old['emergency_team_id']
            && $request['emergency_team_id']
            && $old['emergency_team_id'] != $request['emergency_team_id']  //ส่วนกลาง กดเปลี่ยนทีม
        ) {
            if ($res = self::canCheck($request)) return $res;
            //ส่งแจ้งเตือน หาหัวหน้าทีมเก่า
            LinePostUtility::LineMakeInformation(LineController::Emc_request_end, $emergencyRequest);
            // ส่งให้แค่หัวหน้าทีมเก่า
            $notification = $this->notificationService->createNotification('emergency_request.canceled', ['name' => $name], $emergencyRequest->id, EmergencyRequest::class, false);
            $this->notificationService->sendToTeamLeader($notification, $emergencyRequest->emergency_team_id);

            //ส่งหาหัวหน้าทีมใหม่
            $request['assigner'] = Auth::user()->id;
            LinePostUtility::LineMakeInformation(LineController::Emc_request_post_team, $request);

            // ส่งอันใหม่
            $notification = $this->notificationService->createNotification('emergency_request.assigned', ['name' => $name], $emergencyRequest->id, EmergencyRequest::class, true);
            $this->notificationService->sendToTeamLeader($notification, $request['emergency_team_id']);
            $textResponse = __('success.emc_request.changeTeam');
        }

        if ($emergencyRequest['status'] == EmergencyRequest::approve)  $request['assigner'] = Auth::user()->id;
        $data = $this->emergencyRequestService->update($emergencyRequest, $request->all());
        $data->load('team');
        if ($request->status != $old['status']) {
            if ($emergencyRequest['status'] == EmergencyRequest::waiting && $skip == false) {
                // ส้งไปให้ส่วนกลาง หัวหน้าทีม กด ปฏิเสธ ปะ ?
                LinePostUtility::LineMakeInformation(LineController::Emc_request_unaccept, $emergencyRequest);
                $emergencyRequest->update(['emergency_team_id' => null]);
                $notification = $this->notificationService->createNotification('emergency_request.rejected', ['team' => $data->team?->name, 'name' => $name], $emergencyRequest->id, EmergencyRequest::class, true);
                $this->notificationService->sendToAdmin($notification);
                $textResponse = __('success.emc_request.delTeam');
            } else if ($emergencyRequest['status'] == EmergencyRequest::approve) {
                // ส่งหัวหน้า
                LinePostUtility::LineMakeInformation(LineController::Emc_request_post_team, $emergencyRequest);
                $notification = $this->notificationService->createNotification('emergency_request.assigned', ['team' => $data->team?->name, 'name' => $name], $emergencyRequest->id, EmergencyRequest::class, true);
                $this->notificationService->sendToTeamLeader($notification, $data->emergency_team_id);
                $textResponse = __('success.emc_request.approve');
            } else if ($emergencyRequest['status'] == EmergencyRequest::helping) { //ส่งแจ้งเตือนให้ลูกทีมทราบ
                LinePostUtility::LineMakeInformation(LineController::Emc_assigned, $emergencyRequest);
                $notification = $this->notificationService->createNotification('emergency_request.approved', ['team' => $data->team?->name, 'name' => $name], $emergencyRequest->id, EmergencyRequest::class, true);
                $this->notificationService->sendToAdmin($notification);
                $notification = $this->notificationService->createNotification('emergency_request.assigned', ['name' => $name], $emergencyRequest->id, EmergencyRequest::class, true);
                $this->notificationService->sendToTeamMember($notification, $data->emergency_team_id);
                $textResponse = __('success.emc_request.heling');
            } else if ($emergencyRequest['status'] == EmergencyRequest::complete) { //ส่งแจ้งเตือน ช่วยเหลือสำเร็จ
                self::updatedAssigner($emergencyRequest);
                LinePostUtility::LineMakeInformation(LineController::Emc_success, $emergencyRequest);
                $notification = $this->notificationService->createNotification('emergency_request.success', ['name' => $name], $emergencyRequest->id, EmergencyRequest::class, true);
                $this->notificationService->sendToAdmin($notification);
                $textResponse = __('success.emc_request.complete');
            } else if ($emergencyRequest['status'] == EmergencyRequest::incomplete) { //ส่งแจ้งเตือน ช่วยเหลือไม่สำเร็จ
                self::updatedAssigner($emergencyRequest);
                LinePostUtility::LineMakeInformation(LineController::Emc_unsuccess, $emergencyRequest);
                $notification = $this->notificationService->createNotification('emergency_request.unsuccess', ['name' => $name], $emergencyRequest->id, EmergencyRequest::class, true);
                $this->notificationService->sendToAdmin($notification);
                $textResponse = __('success.emc_request.incomplete');
            }
        }

        if($data->wasChanged('emergency_team_id')){
            EmcRequestNotification::dispatch([$old['emergency_team_id'], $data->emergency_team_id]);
        }
        else if($data->wasChanged('status')){
            EmcRequestNotification::dispatch([$data->emergency_team_id]);
        }

        if (self::isDifferent($emergencyRequest['victim_name'], $request['victim_name'])) $textResponse = __('success.emc_request.victimName');
        // return ['e'=>json_decode($emergencyRequest['victim_name'], true), 'r'=>$request['victim_name']];
        if ($emergencyRequest['status'] == EmergencyRequest::disapprove) $textResponse = __('success.emc_request.disapprove');
        return ApiResponse::successResponse($textResponse ?? __('success.updated'), $emergencyRequest);
    }

    function isDifferent($aJson, array $b): bool
    {
        $a = json_decode($aJson, true);
        sort($a);
        sort($b);
        return json_encode($a) !== json_encode($b);
    }

    private function updatedAssigner($e)
    {
        if (!$e['assigner']) {
            $e->update([
                'assigner' => Auth::user()->id
            ]);
        }
    }

    private function canCheck($e)
    {
        if (!in_array($e['status'], [EmergencyRequest::waiting, EmergencyRequest::approve]))
            return ApiResponse::errorResponse(__('error.emc_req.woking_not_change'));
    }

    private function canEdit($e)
    {
        if (in_array($e['status'], [EmergencyRequest::disapprove, EmergencyRequest::incomplete, EmergencyRequest::complete]))
            return ApiResponse::errorResponse(__('error.emc_req.end_of_job'));
    }

    /**
     * Emergency request | ลบ  ตัวแปร / ปุ้ย .
     */
    public function destroy(EmergencyRequest $emergencyRequest)
    {
        $this->authorize('delete', EmergencyRequest::class);
        $emergencyRequest->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    /**
     * Emergency request | ดึง เหตุผล.
     */
    public function reason()
    {
        $this->authorize('viewAny', EmergencyRequest::class);
        $data = $this->emergencyRequestService->reason();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึง Noti. mock
     */
    public function noti()
    {
        $data = $this->emergencyRequestService->noti();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function phone_detail(Request $request, $phone, $id)
    {
        $data = $this->emergencyRequestService->emcRequestBy($request, $phone, $id); //phone & id
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function requestShelter(Request $request)
    {
        // ให้แต่ทีมที่รับผิดชอบย้ายได้
        $this->authorize('requestShelter', EmergencyRequest::class);

        if ($request->check == true) {
            if ($request->victim_name) {
                $victims = $request->victim_name;
                foreach ($victims as &$i) {
                    $q = Population::select(
                        'populations.*',
                        'shelter_populations.shelter_id',
                        'prefixes.name as prefix_name',

                        'pg.subdistrict_code as p_subdistrict_code',
                        'pg.district_code as p_district_code',
                        'pg.province_code as p_province_code',
                        'pg.postal_code as p_postal_code',

                        'cg.subdistrict_code as c_subdistrict_code',
                        'cg.district_code as c_district_code',
                        'cg.province_code as c_province_code',
                        'cg.postal_code as c_postal_code',
                    )
                        ->where('first_name', $i['fname'])
                        ->where('last_name', $i['lname'])
                        ->leftJoin('geography as pg', 'pg.id', 'populations.p_geography_id')
                        ->leftJoin('geography as cg', 'cg.id', 'populations.c_geography_id')
                        ->leftJoin('prefixes', 'prefixes.id', 'populations.prefix_id')
                        ->leftJoin('shelter_populations', 'populations.id', 'shelter_populations.population_id')
                        ->first();
                    if ($q) {
                        if ($q->shelter_id) {
                            $i['trans_status'] = Population::old_has_shelter;
                            $i['prefix_name'] = $q->prefix_name;
                            $i['shelter_id'] = $q->shelter_id;
                            $i['first_name'] = $i['fname'];
                            $i['lname_name'] = $i['lname'];
                            unset($i['fname'], $i['lname']);
                            $ar[] = $i;
                            $i = array_merge($i, $q->toArray());
                        } else {
                            $i = array_merge($i, $q->toArray());
                            unset($i['fname'], $i['lname']);
                            $i['trans_status'] = Population::old_not_has_shelter;
                        }
                    } else {
                        $i['first_name'] = $i['fname'];
                        $i['last_name'] = $i['lname'];
                        $i['prefix_id'] = $i['prefix_id'] ?? null;
                        unset($i['fname'], $i['lname']);
                        $i['trans_status'] = Population::new_population;
                    }
                }
                return ApiResponse::successResponse(__('success.data'), ['data' => $victims, 'has_shelter' => $ar ?? []]);
            }
        } else {
            $request->validate(
                [
                    'shelter_id' => 'required|exists:shelters,id',
                    'victim_name' => 'array|required',
                    'victim_name.*.prefix_id' => 'required|integer|exists:prefixes,id',
                    'victim_name.*.first_name' => 'required|string|max:100',
                    'victim_name.*.last_name' => 'required|string|max:100',
                    'victim_name.*.birth_date' => 'required|date',
                    'victim_name.*.gender' => 'required|in:0,1,2',
                    'victim_name.*.phone' => 'required|string|size:10',
                    'victim_name.*.p_address' => 'required|string|max:100',
                    'victim_name.*.p_geography_id' => 'required|exists:geography,id',
                    'victim_name.*.c_address' => 'required|string|max:100',
                    'victim_name.*.c_geography_id' => 'required|exists:geography,id',
                    'victim_name.*.same_address' => 'required',
                    'victim_name.*.status' => ['required', Rule::enum(PopulationStatus::class)],
                    'victim_name.*.created_date' => 'required|date',
                    'request_id' => 'required',
                ],
            );
            foreach ($request['victim_name'] as $i) {
                $i['shelter_id'] = $request['shelter_id'];
                $i['birth_date'] = Carbon::parse($i['birth_date'])->format('Y-m-d');
                $i['created_date'] = Carbon::parse($i['created_date'])->format('Y-m-d');
                if ($i['trans_status'] == Population::new_population) {
                    (new PopulationService)->create($i);
                } else {
                    $q = Population::find($i['id']);
                    (new PopulationService)->update($q, $i);
                }
            }
        }
        if ($request->request_id) {
            EmergencyRequest::where('id', $request->request_id)
                ->update([
                    'status' => EmergencyRequest::complete,
                ]);
            EmergencyRequestLog::create([
                'emergency_request_id' => $request->request_id,
                'reason_id' => 99,
                'reason' => 'ย้ายเข้าสู่ศูนย์',
            ]);
        }
        return ApiResponse::successResponse(__('success.emc_request.trans'));
    }
}
