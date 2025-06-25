<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Population\CheckPopulationRequest;
use App\Http\Requests\Population\CreatePopulationRequest;
use App\Http\Requests\Population\ExitAllPopulationRequest;
use App\Http\Requests\Population\TransferPopulationRequest;
use App\Http\Requests\Population\UpdatePopulationRequest;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\TransferHistory;
use App\Services\ImageService;
use App\Services\PopulationService;
use App\Services\ShelterService;
use App\Services\TransferHistoryService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Support\Facades\Http;

class PopulationController extends Controller
{
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
        $data = $this->populationService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create population | สร้างประชากรใหม่
     */
    public function store(CreatePopulationRequest $request)
    {
        $shelter = $request->shelter_id;
        if ($shelter) {
            if ($this->populationService->isShelterMax($shelter)) {
                return ApiResponse::errorResponse(__('ศูนย์พักพิงเต็ม'));
            }
        }
        $data = $this->populationService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Population::IMAGE_FOLDER, Population::class, $data->id);
        if ($shelter) { //ตอนสร้าง ปชช ยังไม่เข้า shelter
            $this->transferLogService->createLog(TransferHistory::ENTER, Population::class, $data->id, Shelter::class, $data->shelter_id);
        }
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    /**
     * Display specific population | แสดงประชากรรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function show(Population $population)
    {
        $data = $this->populationService->show($population);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Update population | อัพเดตข้อมูลประชากรรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function update(UpdatePopulationRequest $request, Population $population)
    {
        $data = $this->populationService->update($population, $request->all());
        $data->image = $this->imageService->updateImage($request->image, Population::IMAGE_FOLDER, Population::class, $data->id);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Transfer population | ย้ายศูนย์พักพิงประชากรรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function transfer(TransferPopulationRequest $request, Population $population)
    {
        $oldShelter_id = $population->shelter_id;
        $population_id = $population->id;
        $newShelter_id = $request->shelter_id;

        if ($newShelter_id == $oldShelter_id) {
            return ApiResponse::successResponse(__('success.updated'));
        }

        $shelter = $this->shelterService->getShelter($newShelter_id);
        $data = $this->populationService->transfer($population, $request->all(), $shelter->max_capacity);
        if ($data['shelter_id'] == $oldShelter_id) {
            return ApiResponse::errorResponse(__('ศูนย์พักพิงเต็ม'));
        } else {
            $this->transferLogService->createLog(TransferHistory::TRANSFER, Population::class, $population_id, Shelter::class, $newShelter_id);
        }
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Exit population | นำประชากรออกจากศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function exit(Population $population)
    {
        $oldShelter_id = $population->shelter_id;
        $population_id = $population->id;

        $request = ['shelter_id' => Population::exit];
        $data = $this->populationService->update($population, $request);
        $this->transferLogService->createLog(TransferHistory::EXIT, Population::class, $population_id, Shelter::class, $oldShelter_id);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Exit all population | นำประชากรออกจากศูนย์พักพิงทั้งหมด
     */
    public function exitall(ExitAllPopulationRequest $request)
    {
        $shelter_id = $request->shelter_id;
        $populations = Population::where('shelter_id', $shelter_id)->get();
        Population::where('shelter_id', $shelter_id)->update(['shelter_id' => null]);
        foreach ($populations as $population) {
            $this->transferLogService->createLog(TransferHistory::EXIT, Population::class, $population->id, Shelter::class, $shelter_id);
        }

        return ApiResponse::successResponse(__('success.updated'));
    }

    /**
     * Check population | เช็คประชากรที่อยู่ในระบบ
     */
    public function checkpopulation(CheckPopulationRequest $request)
    {
        $data = $this->populationService->checkpupulation($request->all());
        if ($data) {
            return ApiResponse::successResponse(__('success.data'), $data);
        } else {
            return ApiResponse::errorResponse(__('ไม่พบประชากร'));
        }
    }

    /**
     * Import population | นำเข้าประชากรจากภายนอก
     */
    public function addexternalpop()
    {
        $data = $this->populationService->addexternalpopulation();
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
