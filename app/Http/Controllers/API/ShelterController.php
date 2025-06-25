<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shelter\CreateShelterRequest;
use App\Http\Requests\Shelter\UpdateShelterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Shelter;
use App\Models\TransferHistory;
use App\Services\ImageService;
use App\Services\PopulationService;
use App\Services\ShelterService;
use App\Services\TransferHistoryService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;

class ShelterController extends Controller
{
    protected $shelterService, $imageService, $populationService, $transferLogService;

    public function __construct(ShelterService $shelterService, ImageService $imageService, PopulationService $populationService, TransferHistoryService $transferLogService)
    {
        $this->shelterService = $shelterService;
        $this->imageService = $imageService;
        $this->populationService = $populationService;
        $this->transferLogService = $transferLogService;
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
        $data = $this->shelterService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Created shelter| สร้างศูนย์พักพิง
     */
    public function store(CreateShelterRequest $request)
    {
        $data = $this->shelterService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Shelter::IMAGE_FOLDER, Shelter::class, $data->id);
        return ApiResponse::createdResponse('Success', $data);
    }

    /**
     * Update the specified shelter| แก้ไขศูนย์พักพิง
     */
    #[PathParameter('shelter', description: 'ส่ง id ของ shelters', type: 'string', example: '3')]
    public function update(UpdateShelterRequest $request, Shelter $shelter)
    {
        if (isset($request->status) && (int) $request->status === Shelter::CLOSED) {
            $isShelterClear = $this->populationService->isShelterClear($shelter->id);
            if (!$isShelterClear) {
                return ApiResponse::errorResponse(('ยังมีผู้ประสบภัยอยู่ในศูนย์พักพิงนี้'));
            }
        }

        $data = $this->shelterService->update($shelter, $request->all());
        $data->image = $this->imageService->updateImage($request->image, Shelter::IMAGE_FOLDER, Shelter::class, $data->id);
        $shelter_id = $data->id;
        $action_type = $data->status == Shelter::CLOSED ? TransferHistory::CLOSE : TransferHistory::OPEN;
        $this->transferLogService->createLog($action_type, Shelter::class, $shelter_id, Shelter::class, $shelter_id);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Show specified shelter| แสดงรายศูนย์พักพิง
     */
    #[PathParameter('shelter', description: 'ส่ง id ของ shelters', type: 'string', example: '3')]
    public function show(Shelter $shelter)
    {
        $data = $this->shelterService->show($shelter);
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
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | create // type name
     */
}
