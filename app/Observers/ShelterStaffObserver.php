<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Models\Shelter;
use App\Models\ShelterPosition;
use App\Models\ShelterStaff;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TransferHistoryService;

class ShelterStaffObserver
{
    protected TransferHistoryService $logService;
    protected NotificationService $notificationService;

    public function __construct(TransferHistoryService $logService, NotificationService $notificationService)
    {
        $this->logService = $logService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the ShelterStaff "created" event.
     */
    public function created(ShelterStaff $shelterStaff): void
    {
        $this->logService->createLog(ActionType::ENTER->value, User::class, $shelterStaff->user_id, Shelter::class, $shelterStaff->shelter_id);
        // Send a notification
        $notification = $this->notificationService->createNotification('shelter_staff.tranfered', ['name' => $shelterStaff->shelter->name], $shelterStaff->id, ShelterStaff::class, false);
        $this->notificationService->sendToSelfUser($notification, $shelterStaff->user_id);

        if ($shelterStaff->shelter_position_id) {
            $this->logService->createLog(ActionType::ASSIGN->value, User::class, $shelterStaff->user_id, ShelterPosition::class, $shelterStaff->shelter_position_id);
             // Send a notification
            $notification = $this->notificationService->createNotification('shelter_position.updated', [], $shelterStaff->id, ShelterStaff::class, false);
            $this->notificationService->sendToSelfUser($notification, $shelterStaff->user_id);
        }
    }

    /**
     * Handle the ShelterStaff "updated" event.
     */
    public function updated(ShelterStaff $shelterStaff): void
    {
        if ($shelterStaff->wasChanged('shelter_id')) {
            $this->logService->createLog(ActionType::TRANSFER->value, User::class, $shelterStaff->user_id, Shelter::class, $shelterStaff->shelter_id);
            $notification = $this->notificationService->createNotification('shelter_staff.tranfered', ['name' => $shelterStaff->shelter->name], $shelterStaff->id, ShelterStaff::class, false);
            $this->notificationService->sendToSelfUser($notification, $shelterStaff->user_id);
        }

        if ($shelterStaff->wasChanged('shelter_position_id') && $shelterStaff->shelter_position_id) {
            $this->logService->createLog(ActionType::ASSIGN->value, User::class, $shelterStaff->user_id, ShelterPosition::class, $shelterStaff->shelter_position_id);
            // Send a notification
            $notification = $this->notificationService->createNotification('shelter_position.updated', [], $shelterStaff->id, ShelterStaff::class, false);
            $this->notificationService->sendToSelfUser($notification, $shelterStaff->user_id);
        }
    }

    /**
     * Handle the ShelterStaff "deleted" event.
     */
    public function deleted(ShelterStaff $shelterStaff): void
    {
        $this->logService->createLog(ActionType::EXIT->value, User::class, $shelterStaff->user_id, Shelter::class, $shelterStaff->shelter_id);
        // Send a notification
        $notification = $this->notificationService->createNotification('shelter_staff.leaved', ['name' => $shelterStaff->shelter->name], $shelterStaff->id, ShelterStaff::class, false);
        $this->notificationService->sendToSelfUser($notification, $shelterStaff->user_id);
    }
}
