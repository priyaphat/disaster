<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Models\Population;
use App\Models\Shelter;
use App\Services\TransferHistoryService;

class PopulationObserver
{
    protected TransferHistoryService $logService;

    public function __construct(TransferHistoryService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Handle the Population "created" event.
     */
    public function created(Population $population): void
    {
        if ($population->shelter_id) {
            $this->logService->createLog(ActionType::ENTER->value, Population::class, $population->id, Shelter::class, $population->shelter_id);;
        }
    }

    /**
     * Handle the Population "updated" event.
     */
    public function updated(Population $population): void
    {
        $oldData = $population->getOriginal();
        $newData = $population->getAttributes();

        if ($population->isDirty('shelter_id') && !$oldData['shelter_id'] && $newData['shelter_id']) {
            $this->logService->createLog(ActionType::ENTER->value, Population::class, $population->id, Shelter::class, $newData['shelter_id']);
        }

        if ($population->isDirty('shelter_id') && $oldData['shelter_id'] && $newData['shelter_id']) {
            $this->logService->createLog(ActionType::TRANSFER->value, Population::class, $population->id, Shelter::class, $newData['shelter_id']);
        }

        if ($population->isDirty('shelter_id') && !$newData['shelter_id']) {
            $this->logService->createLog(ActionType::EXIT->value, Population::class, $population->id, Shelter::class, $oldData['shelter_id']);
        }
    }

}
