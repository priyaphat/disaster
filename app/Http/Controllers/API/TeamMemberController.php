<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\DeleteMemberRequest;
use App\Http\Requests\Member\MemberRequest;
use App\Http\Responses\ApiResponse;
use App\Models\TeamMember;
use App\Services\TeamMemberService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamMemberController extends Controller
{
    use AuthorizesRequests;
    protected $memberService;

    public function __construct(TeamMemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Join a team | เข้าร่วมทีมรายบุคคล
     */
    public function join(MemberRequest $request)
    {
        //
        $this->authorize('create', TeamMember::class);

        $message = $this->memberService->create($request->all());
        return ApiResponse::successResponse($message);
    }

    /**
     * Tranfer a team | ย้ายทีมรายบุคคล
     */
    public function transfer(MemberRequest $request)
    {
        //
        $this->authorize('update', TeamMember::class);

        $discontinue = $request->has('discontinued') ? $request->input('discontinued') : false;
        $message = $this->memberService->update($request->all(), $discontinue);
        return ApiResponse::successResponse($message);
    }

    /**
     * Leave a team | ออกจากทีมรายบุคคล
     */
    public function leave(DeleteMemberRequest $request)
    {
        //
        $this->authorize('delete', TeamMember::class);

        $discontinue = $request->has('discontinued') ? $request->input('discontinued') : false;
        $message = $this->memberService->detach($request->all(), $discontinue);
        return ApiResponse::successResponse($message);
    }
}
