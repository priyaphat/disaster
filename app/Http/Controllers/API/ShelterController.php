<?php

namespace App\Http\Controllers\API;

use App\Enums\ActionType;
use App\Enums\DamagedUtility as EnumsDamagedUtility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shelter\CreateShelterRequest;
use App\Http\Requests\Shelter\UpdateShelterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\DamagedUtility;
use App\Models\Shelter;
use App\Models\TransferHistory;
use App\Services\ImageService;
use App\Services\PopulationService;
use App\Services\ShelterService;
use App\Services\ShelterStaffService;
use App\Services\TransferHistoryService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;

class ShelterController extends Controller
{
    protected $shelterService, $imageService, $populationService, $transferLogService, $staffServeice;

    public function __construct(
        ShelterService $shelterService,
        ImageService $imageService,
        PopulationService $populationService,
        TransferHistoryService $transferLogService,
        ShelterStaffService $staffServeice
    ) {
        $this->shelterService = $shelterService;
        $this->imageService = $imageService;
        $this->populationService = $populationService;
        $this->transferLogService = $transferLogService;
        $this->staffServeice = $staffServeice;
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
        $data = $this->shelterService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Shelter::IMAGE_FOLDER, Shelter::class, $data->id);
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Update the specified shelter| แก้ไขศูนย์พักพิง
     */
    #[PathParameter('shelter', description: 'ส่ง id ของ shelters', type: 'string', example: '3')]
    public function update(UpdateShelterRequest $request, Shelter $shelter)
    {
        // Update only status
        if (isset($request->status)) {
            if ((int) $request->status === Shelter::CLOSED) {
                $this->populationService->updateByShelter(['shelter_id' => $shelter->id]);
            }
            $data = $this->shelterService->update($shelter, $request->only('status'));
            return ApiResponse::successResponse($data->status == Shelter::OPEN ? __('success.shelter.active') : __('success.shelter.inactive'), $data);
        }

        if (!isset($request->utilities)) {
            $request->merge([
                'utilities' => null
            ]);
        }

        // Update all data;
        if($request->max_capacity < $shelter->population->count()){
            return ApiResponse::errorResponse(__('error.shelter.limit'));
        }

        $data = $this->shelterService->update($shelter, $request->all());
        $data->image = $this->imageService->updateImage($request->image, Shelter::IMAGE_FOLDER, Shelter::class, $data->id);
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
        return $this->shelterService->dashboard($request->all());
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | show // type name paginate
     */
    public function dmgs(Request $request, $type = null)
    {
        $data = $this->shelterService->damaged_utilities($request, $type);
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
            'data' => 'required|array',
            'data.*.type' => 'required|in:' . implode(',', array_map(fn($e) => $e->value, EnumsDamagedUtility::cases())),
            'data.*.name' => 'required|string|max:255',
        ], []);
        self::dmg_delete($data['data'][0]['type']);
        if (DamagedUtility::insert($data['data'])) return $data['data'];
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | dalete // type
     */
    public function dmg_delete($damaged)
    {
        DamagedUtility::where('type', $damaged)->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
