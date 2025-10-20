<?php

namespace App\Http\Controllers\API;

use App\Enums\DamagedUtility as EnumsDamagedUtility;
use App\Exports\ShelterExport;
use App\Exports\VerticalUserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\Shelter\CreateShelterRequest;
use App\Http\Requests\Shelter\UpdateShelterRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LinePostUtility;
use App\Http\Utility\ShelterUtility;
use App\Models\DamagedUtility;
use App\Models\DamagedUtilityLogs;
use App\Models\Shelter;
use App\Services\NotificationService;
use App\Services\ShelterService;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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

    public function export(ReportRequest $request, ?Shelter $shelter)
    {
        return $this->shelterService->export($shelter, $request->all());
    }
}
