<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Population\CheckPopulationRequest;
use App\Http\Requests\Population\CreatePopulationRequest;
use App\Http\Requests\Population\UpdatePopulationRequest;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\AssessmentLogs;
use App\Models\Assessments;
use App\Models\Image;
use App\Models\Population;
use App\Models\ShelterPopulation;
use App\Models\TransferHistory;
use App\Services\ImageService;
use App\Services\PopulationService;
use App\Services\ShelterService;
use App\Services\TransferHistoryService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PopulationController extends Controller
{
    use AuthorizesRequests;
    protected $populationService, $shelterService, $transferLogService, $imageService;

    public function __construct(PopulationService $populationService, ShelterService $shelterService, TransferHistoryService $transferLogService, ImageService $imageService)
    {
        $this->populationService = $populationService;
        $this->shelterService = $shelterService;
        $this->transferLogService = $transferLogService;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the team | แสดงรายชื่อประชากร
     */
    #[QueryParameter('per_page', description: 'ส่งจำนวนข้อมูลที่ต้องการต่อหน้า', type: 'string', example: '3')]
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "Railey")]
    #[QueryParameter('gender', description: 'Filter ตามเพศ 0 = ไม่ระบุเพศ, 1 =  ชาย, 2 = หญิง', type: 'string', example: "1")]
    #[QueryParameter('status', description: 'ค้นหาตามสถานะ 0 - เสียชีวิต , 1 - บาดเจ็บ , 2 - ปกติ', type: 'string', example: "2")]
    #[QueryParameter('shelter', description: 'ส่งตัวแปรนี้หากต้องการหาเฉพาะผู้ประสบภัย', type: 'string')]
    #[QueryParameter('shelter_id', description: 'ส่ง shelter_id ของศูนย์พักพิงนั้นๆ', type: 'string', example: "1")]
    #[QueryParameter('tracking', description: 'ค้นหาตามสถานะการติดตามหลังภัยบัติ 0/1', type: 'string', example: "asc, desc, name")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Population::class);

        $data = $this->populationService->getList($request->all());
        $summary = $this->populationService->summary($request->except('sort'));
        return ApiResponse::customSuccessResponse(__('success.data'), [
            'data' => $data,
            'summary' => $summary
        ]);
    }

    /**
     * Create population | สร้างประชากรใหม่
     */
    public function store(CreatePopulationRequest $request)
    {
        $this->authorize('create', Population::class);

        $data = $this->populationService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Display specific population | แสดงประชากรรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function show(Population $population)
    {
        $this->authorize('view', $population);

        $data = $this->populationService->show($population);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Update population | อัพเดตข้อมูลประชากรรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function update(UpdatePopulationRequest $request, Population $population)
    {
        $this->authorize('update', $population);

        $prevShelter = $population->shelter_id;
        $data = $this->populationService->update($population, $request->all());
        // $data->image = $this->imageService->updateImage($request->image, Population::IMAGE_FOLDER, Population::class, $data->id);

        if ($prevShelter == $data->shelterPopulation->shelter_id) {
            return ApiResponse::successResponse(__('success.updated'), $data);
        }

        return ApiResponse::successResponse(__('success.created'), $data);
    }

    /**
     * Check population | เช็คประชากรที่อยู่ในระบบ
     */
    public function checkPopulation(CheckPopulationRequest $request)
    {
        $data = $this->populationService->checkPopulation($request->all());
        if (!$data) {
            return ApiResponse::errorResponse(__('ไม่พบประชากร'));
        }
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Import population | นำเข้าประชากรจากภายนอก
     */
    public function addExternalPop()
    {
        $data = $this->populationService->addExternalPopulation();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

     /**
     * Delete all populations | ลบประชากรทั้งหมด
     */
    public function destroy(Population $population)
    {
        //
        $population->shelterPopulation->delete();
        $population->image->delete();
        $population->assessments->log->delete();
        $population->assessments->delete();
        $population->histories->delete();

        // AssessmentLogs::truncate();
        // Assessments::truncate();
        // ShelterPopulation::truncate();
        // Population::truncate();
        // Image::where('parentable_type', Population::class)->delete();
        // TransferHistory::where('parentable_type', Population::class)->delete();
        return ApiResponse::successResponse(__('success.population.deleted'));
    }

    /**
     * Delete all populations | ลบประชากรทั้งหมด
     */
    public function destroyAll()
    {
        //
        AssessmentLogs::truncate();
        Assessments::truncate();
        ShelterPopulation::truncate();
        Population::truncate();
        Image::where('parentable_type', Population::class)->delete();
        TransferHistory::where('parentable_type', Population::class)->delete();
        return ApiResponse::successResponse(__('success.population.deleted'));
    }
}
