<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserTranfer\DeleteMemberRequest;
use App\Http\Requests\UserTranfer\MemberRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserTransferService as TransferService;

class UserTransferController extends Controller
{
    protected $tranferService;

    public function __construct(TransferService $tranferService)
    {
        $this->tranferService = $tranferService;
    }
    
    /**
     * Join a team | เข้าร่วมทีมรายบุคคล
     */
    public function joinTeam(MemberRequest $request)
    {
        //
        $message = $this->tranferService->createMember($request->all());
        return ApiResponse::successResponse($message);
    }

    /**
     * Tranfer a team | ย้ายทีมรายบุคคล
     */
    public function transferTeam(MemberRequest $request)
    {
        //
        $discontinue = $request->has('discontinued') ? $request->input('discontinued') : false;
        $message = $this->tranferService->updateMember($request->all(), $discontinue);
        return ApiResponse::successResponse($message);
    }

    /**
     * Leave a team | ออกจากทีมรายบุคคล
     */
    public function leaveTeam(DeleteMemberRequest $request)
    {
        //
        $discontinue = $request->has('discontinued') ? $request->input('discontinued') : false;
        $message = $this->tranferService->deleteMember($request->all(), $discontinue);
        return ApiResponse::successResponse($message);
    }

        /**
     * Join a team | เข้าร่วมทีมรายบุคคล
     */
    public function joinShelter(MemberRequest $request)
    {
        //
        $this->tranferService->createShelterStaff($request->all());
        return ApiResponse::successResponse(__('success.staff.selected'));
    }

    /**
     * Tranfer a team | ย้ายทีมรายบุคคล
     */
    public function transferShelter(MemberRequest $request)
    {
        //
        $this->tranferService->updateShelterStaff($request->all());
        return ApiResponse::successResponse(__('success.staff.selected'));
    }

    /**
     * Leave a team | ออกจากทีมรายบุคคล
     */
    public function leaveShelter(DeleteMemberRequest $request)
    {
        $this->tranferService->deleteShelterStaff($request->all());
        return ApiResponse::successResponse(__('success.staff.leaved'));
    }
}
