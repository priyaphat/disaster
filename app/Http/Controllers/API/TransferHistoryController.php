<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LogUtility;
use App\Services\TransferHistoryService;
use Illuminate\Http\Request;

class TransferHistoryController extends Controller
{
    protected $transferLogService;

    public function __construct(TransferHistoryService $transferLogService)
    {
        $this->transferLogService = $transferLogService;
    }

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
