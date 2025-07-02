<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\FilterTeamRequest;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamCollection;
use App\Http\Resources\TeamResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Dedoc\Scramble\Attributes\BodyParameter;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }
    
    /**
     * Display a listing of the team | แสดงรายชื่อทีมแพทย์ (paginate)
     */
    public function index(FilterTeamRequest $request)
    {
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->teamService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create a team | สร้างทีมและสมาชิกในทีม
     */
    public function store(StoreTeamRequest $request)
    {
        //
        $data = $this->teamService->create($request->normalized());
        return ApiResponse::createdResponse(__('success.team.created'), $data);
    }

    /**
     * Show a team | รายละเอียดแต่ละทีม
     */
    public function show(Request $request, Team $team)
    {
        $data = $this->teamService->show($team, $request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Update a team | แก้ไขทีม และสมาชิก
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $data = $this->teamService->update($team, $request->normalized());
        return ApiResponse::successResponse(__('success.team.updated'), $data);
    }

/**
     * Delete a team | ลบทีม และสมาชิก
     */
    public function destroy(Team $team)
    {
        //
        $this->teamService->delete($team);
        return ApiResponse::successResponse(__('success.team.deleted'));
    }
}
