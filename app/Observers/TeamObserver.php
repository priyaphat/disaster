<?php

namespace App\Observers;

use App\Models\Team;
use App\Services\TransferHistoryService;

class TeamObserver
{
    protected TransferHistoryService $logService;

    public function __construct(TransferHistoryService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Handle the Team "deleted" event.
     */
    public function deleted(Team $team): void
    {
        $this->logService->updateLog(Team::class, $team->id, $team->name);
    }
}