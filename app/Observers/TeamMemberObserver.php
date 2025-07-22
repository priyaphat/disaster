<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Http\Controllers\API\LineController;
use App\Http\Utility\LinePostUtility;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\TransferHistoryService;

class TeamMemberObserver
{
    protected TransferHistoryService $logService;

    public function __construct(TransferHistoryService $logService)
    {
        $this->logService = $logService;
    }
    /**
     * Handle the TeamMember "created" event.
     */
    public function created(TeamMember $teamMember): void
    {
        //
        $this->logService->createLog(ActionType::JOIN_TEAM->value, User::class, $teamMember->user_id, Team::class, $teamMember->team_id);
        LinePostUtility::LineMakeInformation(LineController::User_join, $teamMember);
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
        }
    }

    /**
     * Handle the TeamMember "deleted" event.
     */
    public function deleted(TeamMember $teamMember): void
    {
        $this->logService->createLog(ActionType::EXIT_TEAM->value, User::class, $teamMember->user_id, Team::class, $teamMember->team_id);
        LinePostUtility::LineMakeInformation(LineController::User_leave, $teamMember);
    }
}
