<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\DeleteMemberRequest;
use App\Http\Requests\Member\MemberRequest;
use App\Http\Responses\ApiResponse;
use App\Services\TeamMemberService;

class TeamMemberController extends Controller
{
    protected $memberService;

    public function __construct(TeamMemberService $memberService)
    {
        $this->memberService = $memberService;
    }
    
    /**
     * Join a team | เข้าร่วมทีมรายบุคคล
     */
    public function store(MemberRequest $request)
    {
        //
        $this->memberService->create($request->all());
        return ApiResponse::successResponse(__('success.data'));
    }

    /**
     * Tranfer a team | ย้ายทีมรายบุคคล
     */
    public function update(MemberRequest $request)
    {
        //
        $discontinue = $request->has('discontinued') ? $request->input('discontinued') : false;
        $this->memberService->update($request->all(), $discontinue);
        return ApiResponse::successResponse(__('success.data'));
    }

    /**
     * Leave a team | ออกจากทีมรายบุคคล
     */
    public function destroy(DeleteMemberRequest $request)
    {
        //
        $discontinue = $request->has('discontinued') ? $request->input('discontinued') : false;
        $this->memberService->delete($request->all(), $discontinue);
        return ApiResponse::successResponse(__('success.data'));
    }
}
