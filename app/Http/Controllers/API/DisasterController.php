<?php

namespace App\Http\Controllers\API;

use App\Enums\FloodSourceType;
use App\Http\Controllers\Controller;
use App\Services\DisasterService;
use App\Http\Responses\ApiResponse;
use App\Models\Disaster;
use App\Http\Requests\DisasterRequest;
use App\Models\DisasterFlood;
use App\Enums\FloodStatus;
use App\Enums\SmogStatus;
use App\Exports\DisasterExport;
use App\Http\Utility\LinePostUtility;
use App\Models\DisasterEarthquake;
use App\Models\DisasterLandslide;
use App\Models\DisasterSmog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use PDO;

class DisasterController extends Controller
{
    protected $disasterService;

    public function __construct(DisasterService $disasterService)
    {
        $this->disasterService = $disasterService;
    }

    /**
     * Disaster | ดึง Disaster
     */
    public function index()
    {
        $data = $this->disasterService->index();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | ดึง Disaster by id 
     */
    public function show(Disaster $disaster)
    {
        $data = $this->disasterService->show($disaster);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | สร้าง Disaster ตัวแปร / ปุ้ย
     */
    public function store(DisasterRequest $request)
    {
        $data = $this->disasterService->create($request->all());
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    /**
     * Disaster | อับเดต Disaster ตัวแปร / ปุ้ย
     */
    public function update(DisasterRequest $request, Disaster $disaster)
    {
        $data = $this->disasterService->update($disaster, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Disaster | ลบ Disaster
     */
    public function destroy(Disaster $disaster)
    {
        $disaster->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }


    /*####################### Disaster menu #######################*/

    //earthquake - eq

    /**
     * Disaster | สร้าง earthquake
     */
    public function dst_eq_create(DisasterRequest $r)
    {
        $earthquake = DisasterEarthquake::create($r->all());
        LinePostUtility::line_oa_call(LineController::Disaster_earthquake, $earthquake->toArray());
        return ApiResponse::successResponse(__('success.created'), $earthquake);
    }

    /**
     * Disaster | ดึง earthquake
     */
    public function dst_eq_show(Request $request, $earthquake = null)
    {
        $data = $this->disasterService->eq_show($request, $earthquake);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | อัพเดต earthquake
     */
    public function dst_eq_update(DisasterRequest $request, DisasterEarthquake $earthquake)
    {
        $earthquake->update($request->all());
        LinePostUtility::line_oa_call(LineController::Disaster_earthquake, $earthquake->toArray());
        return ApiResponse::successResponse(__('success.sys_updated'), $earthquake);
    }

    public function dst_eq_delete(DisasterEarthquake $id)
    {
        $id->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    //flood - fl

    /**
     * Disaster | ดึง flood
     */
    public function dst_fl_create(DisasterRequest $request)
    {
        $flood = DisasterFlood::create($request->all());
        LinePostUtility::line_oa_call(LineController::Disaster_flood, $flood->toArray());
        return ApiResponse::successResponse(__('success.created'), $flood);
    }

    /**
     * Disaster | ดึง flood
     */
    public function dst_fl_show(Request $request, $flood = null)
    {
        $data = $this->disasterService->fl_show($request, $flood);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | ดึง flood status
     */
    public function dst_fl_status()
    {
        $data = array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], FloodStatus::cases());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | ดึง flood source type
     */
    public function dst_fl_source()
    {
        $data = array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], FloodSourceType::cases());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | อัพเดต flood
     */
    public function dst_fl_update(DisasterRequest $request, DisasterFlood $flood)
    {
        $flood->update($request->all());
        LinePostUtility::line_oa_call(LineController::Disaster_flood, $flood->toArray());
        return ApiResponse::successResponse(__('success.sys_updated'), $flood);
    }

    /**
     * Disaster | ลบ flood
     */
    public function dst_fl_delete(DisasterFlood $flood)
    {
        $flood->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    //landslide

    /**
     * Disaster | ดึง landslide
     */
    public function dst_ls_show(Request $request, $landslide = null)
    {
        $data = $this->disasterService->ls_show($request, $landslide);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /** 
     * Disaster | สร้าง landslide
     */
    public function dst_ls_create(DisasterRequest $request)
    {
        $data = $this->disasterService->ls_create($request);
        LinePostUtility::line_oa_call(LineController::Disaster_landslide, $data->toArray());
        return ApiResponse::successResponse(__('success.sys_created'), $data);
    }

    /**
     * Disaster | โหลด ไฟล์ landslide
     */
    public function dst_ls_download($file)
    {
        return $this->disasterService->download($file);
    }

    /**
     * Disaster | ลบ landslide
     */
    public function dst_ls_delete(DisasterLandslide $id)
    {
        $file = DisasterLandslide::FILE . '/' . $id->file_id;
        if (Storage::exists($file)) Storage::delete($file);
        $id->delete();
        return ApiResponse::successResponse(__('success.disaster.dst_ls_delete'));
    }

    //wildfire

    /**
     * Disaster | ดึง wildfire
     */
    public function dst_wf_show(Request $request)
    {
        $data = $this->disasterService->dst_wf_show($request);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | สร้าง wildfire ตัวแปร / ปุ้ย
     */
    public function dst_wf_create(DisasterRequest $request)
    {
        $data = $this->disasterService->dst_wf_create($request);
        return ApiResponse::successResponse(__('success.sys_updated'), $data);
    }

    //smog

    /**
     * Disaster | สร้าง smog ตัวแปร / ปุ้ย
     */
    public function dst_sm_create(DisasterRequest $request)
    {
        $smog = DisasterSmog::create($request->all());
        LinePostUtility::line_oa_call(LineController::Disaster_smog, $smog->toArray());
        return ApiResponse::successResponse(__('success.created'), $smog);
    }

    /**
     * Disaster | smog show data / ปุ้ย
     */
    public function dst_sm_show(Request $request, $smog = null)
    {
        $data = $this->disasterService->dst_sm_show($request, $smog);
        // if($data->isEmpty()) return  ApiResponse::notFoundResponse(__('error.model_not_found'));
        return  ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | ดึง smog status
     */
    public function dst_sm_status()
    {
        $data = array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], SmogStatus::cases());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Disaster | อัพเดต smog
     */
    public function dst_sm_update(DisasterRequest $request, DisasterSmog $smog)
    {
        $smog->update($request->all());
        LinePostUtility::line_oa_call(LineController::Disaster_smog, $smog->toArray());
        return  ApiResponse::successResponse(__('success.data'), $smog);
    }

    /**
     * Disaster | ลบ smog
     */
    public function dst_sm_delete(DisasterSmog $smog)
    {
        $smog->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    public function exp(Request $r)
    {
        $case = $r->disaster;
        // $case = 'wildfire';
        if (!$case) return ApiResponse::errorResponse(__('error.model_not_found'));
        $rows = [];
        $columns = [];
        if ($case == 'flood') {
            $q = $this->disasterService->fl_show(request(), null, 'get');
            $rows = $q->map(function ($i) {
                return [
                    $i['created_at'],
                    $i['water_source'],
                    $i['address'],
                    FloodSourceType::tryFrom($i['water_source_type'])?->label() ?? '',
                    FloodStatus::tryFrom($i['status'])->label() ?? '',
                    $i['water_value'],
                    $i['Resources'],
                ];
            })->toArray();
            $columns = ['อัปเดตเมื่อ', 'ชื่อแหล่งน้ำ', 'ตำแหน่งเครื่องวัด', 'ประเภทแหล่งน้ำ', 'สถานะ', 'ปริมาณน้ำในแหล่งน้ำ', 'แหล่งข้อมูล'];
        } else if ($case == 'landslide') {
            $q = $this->disasterService->ls_show(request(), null, 'get');
            $rows = $q->map(function ($i) {
                return [
                    $i['created_at'],
                    $i['name'],
                    $i['link'],
                ];
            })->toArray();
            $columns = ['อัปเดตเมื่อ', 'รายละเอียด', 'ไฟล์แนบ'];
        } else if ($case == 'wildfire') {
            $q = $this->disasterService->dst_wf_show(request(), null, 'get');
            for ($i = 0; $i <= 5; $i++) {
                $row = [];
                $row[] = ($q['first_graph']['labels'][$i] ?? '') . ': ' . ($q['first_graph']['model'][$i] ?? '');

                if ($q['second_graph']['model'][$i]['district_name']) {
                    $row[] = ($q['second_graph']['model'][$i]['district_name'] ?? '') . ': ' . ($q['second_graph']['model'][$i]['viirs'] ?? '');
                }

                $rows[] = $row;
            }
            $columns = ['จุดความร้อนตามพื้นที่ใช้ประโยชน์ที่ดิน VIIRS', '5 อันดับ พื้นที่ที่มีจุดความร้อน VIIRS (เชียงราย)'];
        } else if ($case == 'smog') {
            $q = $this->disasterService->dst_sm_show(request(), null, 'get');
            $rows = $q->map(function ($i) {
                return [
                    $i['created_at'],
                    $i['source'],
                    $i['address'],
                    $i['smog_value'],
                    $i['Resources'],
                ];
            })->toArray();
            $columns = ['อัปเดตเมื่อ', 'ชื่อจุดตรวจวัด', 'ตำแหน่งจุดตรวจวัด', 'ค่ามลพิษ', 'แหล่งข้อมูล'];
        } else if ($case == 'earthquake') {
            $q = $this->disasterService->eq_show(request(), null, 'get');
            $rows = $q->map(function ($i) {
                return [
                    $i['created_at'],
                    $i['address'],
                    $i['latitude'] . ',' . $i['longitude'],
                    $i['depth'],
                    $i['magnitude'],
                    $i['occurrence_time'],
                    $i['Resources'],
                ];
            })->toArray();
            $columns = ['อัปเดตเมื่อ', 'จุดศูนย์กลาง', 'ละติจูด,ลองติจูด', 'ลึกจากผิวดิน', 'ระดับความลึกรุนแรง', 'วัน-เวลา ที่เกิดเหตุ', 'แหล่งข้อมูล'];
        }
        return Excel::download(new DisasterExport($columns, $rows), $case . '-Export.xlsx');
    }
}
