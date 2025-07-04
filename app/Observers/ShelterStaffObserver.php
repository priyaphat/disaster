<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Models\Shelter;
use App\Models\ShelterPosition;
use App\Models\ShelterStaff;
use App\Models\TransferHistory;
use App\Models\User;
use App\Services\TransferHistoryService;

class ShelterStaffObserver
{
    protected TransferHistoryService $logService;

    public function __construct(TransferHistoryService $logService)
    {
        $this->logService = $logService;
    }
    
    /**
     * Handle the ShelterStaff "created" event.
     */
    public function created(ShelterStaff $shelterStaff): void
    {
        $this->logService->createLog(ActionType::ENTER->value, User::class, $shelterStaff->user_id, Shelter::class, $shelterStaff->shelter_id);
        if($shelterStaff->shelter_position_id){
            $this->logService->createLog(ActionType::ASSIGN->value, User::class, $shelterStaff->user_id, ShelterPosition::class, $shelterStaff->shelter_position_id);
        }
    }

    /**
     * Handle the ShelterStaff "updated" event.
     */
    public function updated(ShelterStaff $shelterStaff): void
    {
        if ($shelterStaff->isDirty('shelter_id')) {
            $this->logService->createLog(ActionType::TRANSFER->value, User::class, $shelterStaff->user_id, Shelter::class, $shelterStaff->shelter_id);
        }

        if ($shelterStaff->isDirty('shelter_position_id') && $shelterStaff->shelter_position_id) {
            $this->logService->createLog(ActionType::ASSIGN->value, User::class, $shelterStaff->user_id, ShelterPosition::class, $shelterStaff->shelter_position_id);
        }
    }

    /**
     * Handle the ShelterStaff "deleted" event.
     */
    public function deleted(ShelterStaff $shelterStaff): void
    {
         $this->logService->createLog(ActionType::EXIT->value, User::class, $shelterStaff->user_id, Shelter::class, $shelterStaff->shelter_id);
    }
}
