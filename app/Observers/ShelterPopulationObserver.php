<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterPopulation;
use App\Services\TransferHistoryService;

class ShelterPopulationObserver
{
    protected TransferHistoryService $logService;

    public function __construct(TransferHistoryService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Handle the Shelter Population "created" event.
     */
    public function created(ShelterPopulation $shelterPop): void
    {
        if ($shelterPop->shelter_id) {
            $this->logService->createLog(ActionType::ENTER->value, Population::class, $shelterPop->population_id, Shelter::class, $shelterPop->shelter_id);
        }
    }

    /**
     * Handle the Shelter Population "updated" event.
     */
    public function updated(ShelterPopulation $shelterPop): void
    {
        $oldData = $shelterPop->getOriginal();
        $newData = $shelterPop->getAttributes();

        if ($shelterPop->wasChanged('shelter_id') && !$oldData['shelter_id'] && $newData['shelter_id']) {
            $this->logService->createLog(ActionType::ENTER->value, Population::class, $shelterPop->population_id, Shelter::class, $newData['shelter_id']);
        }

        if ($shelterPop->wasChanged('shelter_id') && $oldData['shelter_id'] && $newData['shelter_id']) {
            $this->logService->createLog(ActionType::TRANSFER->value, Population::class, $shelterPop->population_id, Shelter::class, $newData['shelter_id']);
        }

        if ($shelterPop->wasChanged('shelter_id') && !$newData['shelter_id']) {
            $this->logService->createLog(ActionType::EXIT->value, Population::class, $shelterPop->population_id, Shelter::class, $oldData['shelter_id']);
        }
    }
}
