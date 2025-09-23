<?php

namespace App\Http\Controllers\API;

use App\Enums\DamagedUtility as EnumsDamagedUtility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shelter\CreateShelterRequest;
use App\Http\Requests\Shelter\UpdateShelterRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LinePostUtility;
use App\Models\DamagedUtility;
use App\Models\DamagedUtilityLogs;
use App\Models\Shelter;
use App\Services\ShelterService;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShelterController extends Controller
{
    use AuthorizesRequests;
    protected $shelterService;

    public function __construct(
        ShelterService $shelterService,
    ) {
        $this->shelterService = $shelterService;
    }

    /**
     * Display a listing of shelter| แสดงรายการศูนย์พักพิง
     */
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "Shelter 00")]
    #[QueryParameter('status', description: 'ค้นหาตามสถานะการเปิด-ปิด ศูนย์พักพิง 0 = ปิด 1 = เปิด', type: 'string', example: "1")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    #[QueryParameter('district_code', description: 'ค้นหาตามอำเภอ', type: 'string', example: "5701")]
    public function index(Request $request)
    {
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->shelterService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Created shelter| สร้างศูนย์พักพิง
     */
    public function store(CreateShelterRequest $request)
    {
        $this->authorize('create', Shelter::class);

        $data = $this->shelterService->create($request->all());
        (new LineController)->LineNotification($data->toArray(), LineController::Shelter_post);
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Update the specified shelter| แก้ไขศูนย์พักพิง
     */
    #[PathParameter('shelter', description: 'ส่ง id ของ shelters', type: 'string', example: '3')]
    public function update(UpdateShelterRequest $request, Shelter $shelter)
    {
        $this->authorize('update', $shelter);

        // Update only status
        if ($request->has('status') && count($request->all()) === 1) {
            $data = $this->shelterService->update($shelter, $request->only('status'));
            return ApiResponse::successResponse($data->status == Shelter::OPEN ? __('success.shelter.active') : __('success.shelter.inactive'), $data);
        }

        if (!isset($request->utilities)) {
            $request->merge([
                'utilities' => null
            ]);
        }

        // Update all data;
        if ($request->max_capacity < $shelter->populations->count()) {
            return ApiResponse::errorResponse(__('error.shelter.limit'));
        }

        $data = $this->shelterService->update($shelter, $request->all(), true);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Show specified shelter| แสดงรายศูนย์พักพิง
     */
    #[PathParameter('shelter', description: 'ส่ง id ของ shelters', type: 'string', example: '3')]
    public function show(Request $request, Shelter $shelter)
    {
        $data = $this->shelterService->show($shelter, $request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Display a listing of utilities| แสดงรายการสาธารณูปโภคของศูนย์พักพิง
     */
    public function utility()
    {
        $data = $this->shelterService->utility();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dashboard(Request $request)
    {
        $data = $this->shelterService->dashboard($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | show // type name paginate
     */
    public function dmgs(Request $request, $type = null)
    {
        $data = $this->shelterService->damaged_utilities($request->all(), $type);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | ดึง ชื่อ
     */
    public function dmg_name()
    {
        $data = $this->shelterService->utilities();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | create // type, name
     */
    public function dmg_stroe(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_map(fn($e) => $e->value, EnumsDamagedUtility::cases())),
            'name' => 'required|string|max:255',
            'phone' => 'string|max:10',
            'address' => 'string|max:255',
            'geography_id' => 'exists:geography,id',
        ]);

        if ($data = DamagedUtility::create($data)) {
            self::dul_log($data, DamagedUtilityLogs::created);
            (new LineController)->LineNotification($data->toArray(), LineController::Damaged_utility_post);
            return ApiResponse::successResponse(__('success.damaged_utility.created'), $data);
        }
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | updated // type, name
     */
    public function dmg_update(Request $request, DamagedUtility $id)
    {
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_map(fn($e) => $e->value, EnumsDamagedUtility::cases())),
            'name' => 'required|string|max:255',
            'phone' => 'string|max:10',
            'address' => 'string|max:255',
            'geography_id' => 'exists:geography,id',
        ], []);

        if ($id->update($data)) {
            self::dul_log($id, DamagedUtilityLogs::updated);
            (new LineController)->LineNotification($id->toArray(), LineController::Damaged_utility_post);
            return ApiResponse::successResponse(__('success.damaged_utility.created'), $this->shelterService->damaged_utility($id->id));
        }
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | dalete // type
     */
    public function dmg_delete(DamagedUtility $damaged)
    {
        $damaged->delete();
        self::dul_log($damaged, DamagedUtilityLogs::deleted);
        (new LineController)->LineNotification($damaged->toArray(), LineController::Damaged_utility_end);
        return ApiResponse::successResponse(__('success.damaged_utility.deleted'));
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย log 
     */
    public function damaged_utility_logs(Request $r)
    {
        $q = DamagedUtilityLogs::select(
            'damaged_utility_logs.*',
            DB::raw('concat(users.firstname," ",users.lastname) as created_name')
        )
            ->leftJoin('users', 'damaged_utility_logs.created_by', 'users.id')
            ->when(
                $r->search,
                fn($q) =>
                $q->where(DB::raw('concat(users.firstname," ",users.lastname)'), 'like', '%' . $r->search . '%')
            )
            ->when($r->start_date && $r->end_date, fn($q) =>
            $q->whereBetween('damaged_utility_logs.created_at', [
                Carbon::parse($r->start_date)->startOfDay(),
                Carbon::parse($r->end_date)->endOfDay()
            ]))
            ->when(
                $r->type,
                fn($q) =>
                $q->where('damaged_utility_logs.type', $r->type)
            )
            ->when(
                $r->status,
                fn($q) =>
                $q->where('damaged_utility_logs.status', $r->status)
            )->paginate($r->paginate ?? 10);
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    private function dul_log($data, $status)
    {
        $q = new DamagedUtilityLogs();
        if ($status == DamagedUtilityLogs::created) $data['datail'] = 'เพิ่มสถานที่ ' . $data['name'];
        if ($status == DamagedUtilityLogs::updated) $data['datail'] = 'แก้ไขสถานที่ ' . $data['name'];
        if ($status == DamagedUtilityLogs::deleted) $data['datail'] = 'ลบสถานที่ ' . $data['name'];
        $q['datail'] = $data;
        $q['type'] = $data['type'];
        $q['status'] = $status;
        $q->save();
    }
}
