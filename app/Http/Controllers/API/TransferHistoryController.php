<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LogUtility;
use App\Services\TransferHistoryService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;

class TransferHistoryController extends Controller
{
    protected $transferLogService;

    public function __construct(TransferHistoryService $transferLogService)
    {
        $this->transferLogService = $transferLogService;
    }

    #[PathParameter('id', description: 'id ของ model นั้นๆ', type: 'string', example: '3')]
    #[PathParameter('model', description: 'model ที่มีการใช้งาน log ดังนี้ team, shelter, staff, population, user ', type: 'string', example: 'population')]
    #[QueryParameter('search', description: 'ค้นหาชื่อ shelter', type: 'string', example: "Shelter 00")]
    #[QueryParameter('action_type', description: 'ค้นหาตามการกระทำ https://disaster-backend.gentechsoft.tech/api/docs#/operations/transferHistory.actiontype', type: 'string', example: "1")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    #[QueryParameter('district_code', description: 'ค้นหาตามอำเภอ https://disaster-backend.gentechsoft.tech/api/docs#/operations/address.getDistricts', type: 'string', example: "5701")]
    #[QueryParameter('year', description: 'ค้นหาตามปี', type: 'datetime', example: "2025-6-17 00:00:00")]
    #[QueryParameter('start_date', description: 'ค้นหาวัน (ระยะ)', type: 'datetime', example: "2025-6-17 00:00:00")]
    #[QueryParameter('end_date', description: 'ค้นหาวัน (ระยะ)', type: 'datetime', example: "2025-6-20 00:00:00")]
    public function getlog(Request $request, $model, $id)
    {
        return $this->transferLogService->getlog($request->all(), LogUtility::getClassName($model), $id);
    }

    public function shelterlog(Request $request)
    {
        return $this->transferLogService->getshelterlog($request->all());
    }

    public function actiontype()
    {
        $data = $this->transferLogService->getactiontype();
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
