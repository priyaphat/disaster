<?php

namespace App\Services;

use App\Http\Resources\TeamResource;
use App\Http\Utility\LogUtility;
use App\Models\AppointmentLog;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Carbon\Carbon;

class TeamService
{
    /**
     * Get all Teames with filter and limit number.
     */
    public function getList(array $filters = [], bool $paginate = true, int $perPage = 10)
    {
        if (!$paginate) {
            return Team::filter($filters)->latest()->get();
        } else {
            $data = Team::filter($filters)->with('leader', 'members', 'allUsers')->latest()->paginate($perPage);
            $paginate = $data->setCollection(TeamResource::collection($data->items())->collection);
            return  $paginate;
        }
    }

    /**
     * Get a Team by its id.
     */
    public function getById(int $id): Team
    {
        return Team::with('members')->findOrFail($id);
    }

    /**
     * Create a Team.
     */
    public function create(array $data): Team
    {
        $team = Team::create($data);
        $team->team_members()->createMany($data['members']);
        return $team;
    }

    /**
     * Show a Team.
     */
    public function show(Team $team, array $search)
    {
        $teamLog = null;
        if (isset($search['date'])) {
            $logDate = Carbon::parse($search['date'])->format('Y-m-d');
            $teamLog = AppointmentLog::where('parentable_id', $team->id)
                ->where('parentable_type', Team::class)->where('log_date', $logDate)->first();
        }

        if ($teamLog) {
            return $teamLog;
        }

        $team->load(['leader', 'allUsers']);
        return new TeamResource($team);
    }

    /**
     * Update a Team.
     */
    public function update(Team $team, array $data): Team
    {
        $team->update($data);
        $team->syncMembers($data['members']);
        return $team;
    }

    /**
     * Delete a Team.
     */
    public function delete(Team $team): bool
    {
        // Save team log before delete
        $teamData = [
            'parentable_id' => $team->id,
            'parentable_type' => Team::class,
            'description' => $team,
            'log_date' => Carbon::now()->format('Y-m-d')
        ];

        $memberData = [
            'parentable_id' => $team->id,
            'parentable_type' => User::class,
            'description' => $team->allUsers,
            'log_date' => Carbon::now()->format('Y-m-d')
        ];

        LogUtility::createAppointment($teamData);
        LogUtility::createAppointment($memberData);

        $team->upcomingAppointments()->delete();
        $team->team_members->each->delete();
        return $team->delete();
    }

    /**
     * Create a Team.
     */
    public function createMember(array $data): TeamMember
    {
        return TeamMember::create($data);
    }

    /**
     * Update a member.
     */
    public function updateMember(TeamMember $team, array $data): TeamMember
    {
        $team->update($data);
        return $team;
    }


    /**
     * Delete a member.
     */
    public function deleteMember(TeamMember $team, array $data): TeamMember
    {
        $team->update($data);
        return $team;
    }
}
