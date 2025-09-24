<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Http\Controllers\API\LineController;
use App\Http\Utility\LinePostUtility;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TransferHistoryService;
use Illuminate\Support\Facades\Log;

class TeamMemberObserver
{
    protected TransferHistoryService $logService;
    protected NotificationService $notificationService;

    public function __construct(TransferHistoryService $logService, NotificationService $notificationService)
    {
        $this->logService = $logService;
        $this->notificationService = $notificationService;
    }
    /**
     * Handle the TeamMember "created" event.
     */
    public function created(TeamMember $teamMember): void
    {
        //
        Log::info('Creating member for: ' . $teamMember->user_id);
        $this->logService->createLog(ActionType::JOIN_TEAM->value, User::class, $teamMember->user_id, Team::class, $teamMember->team_id);
        LinePostUtility::LineMakeInformation(LineController::User_join, $teamMember);

        $notification = $this->notificationService->createNotification('team.joined', ['name' => $teamMember->team->name,], $teamMember->id, TeamMember::class, false);
        $this->notificationService->sendToSelfUser($notification, $teamMember->user_id);

        // Add leader or member role to user
        $teamMember->user->syncRoles($teamMember->team_position == 1 ? Role::TEAM_LEADER : Role::TEAM_MEMBER);
    }

    /**
     * Handle the TeamMember "updated" event.
     */
    public function updated(TeamMember $teamMember): void
    {
        // ข้อมูลก่อนอัปเดต
        $oldData = $teamMember->getOriginal();
        $newData = $teamMember->getAttributes();

        if ($teamMember->wasChanged('team_id')) {
            $this->logService->createLog(ActionType::EXIT_TEAM->value, User::class, $oldData['user_id'], Team::class, $oldData['team_id']);
            $this->logService->createLog(ActionType::JOIN_TEAM->value, User::class, $newData['user_id'], Team::class, $newData['team_id']);
            LinePostUtility::LineMakeInformation(LineController::User_join, $newData);

            // $notification = $this->notificationService->createNotification('team.leaved', ['name' => $oldData['team']['name'] ?? 'ไม่มีทีม'], $teamMember->id, TeamMember::class, false);
            // $this->notificationService->sendToSelfUser($notification, $teamMember->user_id);
            $notification = $this->notificationService->createNotification('team.joined', ['name' => $teamMember->team->name], $teamMember->id, TeamMember::class, false);
            $this->notificationService->sendToSelfUser($notification, $teamMember->user_id);
        }
        // Add leader or member role to user
        $teamMember->user->syncRoles($teamMember->team_position->value == 1 ? Role::TEAM_LEADER : Role::TEAM_MEMBER);
    }

    /**
     * Handle the TeamMember "deleted" event.
     */
    public function deleted(TeamMember $teamMember): void
    {
        Log::info('Deleting member for: ' . $teamMember->user_id);
        $this->logService->createLog(ActionType::EXIT_TEAM->value, User::class, $teamMember->user_id, Team::class, $teamMember->team_id);
        LinePostUtility::LineMakeInformation(LineController::User_leave, $teamMember);

        $notification = $this->notificationService->createNotification('team.leaved', ['name' => $teamMember->team->name], $teamMember->id, TeamMember::class, false);
        $this->notificationService->sendToSelfUser($notification, $teamMember->user_id);

        // Remove all roles from user
        $teamMember->user->syncRoles([]);
    }
}
