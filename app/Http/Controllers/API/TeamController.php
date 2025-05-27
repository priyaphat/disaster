<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Team;
use App\Models\TeamMember;
use App\Services\TeamService;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = $this->teamService->getList([]);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        //
        $data = $this->teamService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return ApiResponse::successResponse(__('success.data'), $team);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $data = $this->teamService->update($team, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        //
        $this->teamService->delete($team);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
