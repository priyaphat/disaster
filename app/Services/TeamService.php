<?php

namespace App\Services;

use App\Http\Resources\TeamResource;
use App\Http\Utility\LogUtility;
use App\Models\AppointmentLog;
use App\Models\EmergencyRequest;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    public function show($id, array $search)
    {
        $team = Team::find($id);
        $teamLog = null;

        // If request has date, find team log
        if (isset($search['log_id']) && $search['log_id']) {
            $teamLog = AppointmentLog::find($search['log_id']);
        }

        // If the team has log, show a log.
        if ($teamLog) {
            $data = $teamLog->description;
            $allUsers = collect($data['all_members'])->pluck('id');
            $users = User::select('id', 'prefix_id', 'firstname', 'lastname')->whereIn('id', $allUsers)->with('image')->get();
            $data['all_members'] = collect($data['all_members'])->map(function ($item) use ($users) {
                $item['image'] = $users->where('id', $item['id'])->first()->image?->image_path;
                return $item;
            });
            return $data;
        }

        if (!$teamLog && !$team) {
            throw new ModelNotFoundException();
        }

        // Show a current data
        $team->load(['leader', 'allUsers']);
        return new TeamResource($team);
    }

    /**
     * Update a Team.
     */
    public function update(Team $team, array $data): Team
    {
        $oldLeader = $team->leader->id;
        $team->update($data);
        $team->syncMembers($data['members']);
        $team->refresh();
        $newLeader = $team->leader->id;

        // Send Leader updated notification
        if ($oldLeader != $newLeader) {
            $notificationService = app(NotificationService::class);
            $notification = $notificationService->createNotification('team.updated_leader', ['name' => $team->leader->fullname ?? null,], $team->id, Team::class, false);
            $notificationService->sendToTeam($notification, $team->id);
        }
        return $team;
    }

    /**
     * Delete a Team.
     */
    public function delete(Team $team): bool
    {
        // Check the team is in progress
        if ($team->inProgressEmergencyRequests()->exists()) {
            throw new BadRequestHttpException(__('error.team.in_progress'));
        }

        // Update the team name on each emergency request
        // EmergencyRequest::where('emergency_team_id', $team->id)->update(['emergency_team_name' => $team->name]);
        app(AppointmentService::class)->deleteUpcomingAppointment(['team_id' => $team->id]);
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
