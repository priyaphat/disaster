<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Models\Shelter;
use App\Services\TransferHistoryService;

class ShelterObserver
{
    protected TransferHistoryService $logService;

    public function __construct(TransferHistoryService $logService)
    {
        $this->logService = $logService;
    }
    
    /**
     * Handle the ShelterStaff "created" event.
     */
    public function created(Shelter $shelter): void
    {
       $this->logService->createLog(ActionType::CREATE_SHELTER->value, Shelter::class, $shelter->id, Shelter::class, $shelter->id);
    }

    /**
     * Handle the ShelterStaff "updated" event.
     */
    public function updated(Shelter $shelter): void
    {
        $newData = $shelter->getAttributes();
        if ($shelter->wasChanged('status')) {
            $this->logService->createLog($newData == Shelter::OPEN ? ActionType::OPEN->value : ActionType::CLOSE->value, Shelter::class, $shelter->id, Shelter::class, $shelter->id);
        }
    }
}
