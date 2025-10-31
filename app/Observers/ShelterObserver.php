<?php

namespace App\Observers;

use App\Enums\ActionType;
use App\Http\Controllers\API\LineController;
use App\Http\Controllers\API\ShelterProductController;
use App\Http\Resources\ShelterResource;
use App\Models\Shelter;
use App\Services\AppointmentService;
use App\Services\NotificationService;
use App\Services\ShelterPopulationService;
use App\Services\TransferHistoryService;

class ShelterObserver
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
    public function created(Shelter $shelter): void
    {
        $line_data = new ShelterResource($shelter);

        // Send an opened or closed shelter notification
        if ($shelter->status == Shelter::OPEN) {
            $notification = $this->notificationService->createNotification('shelter.opened', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
            $this->notificationService->sendToAllUsers($notification);
            (new LineController)->LineNotification($line_data->toArray(request()), LineController::Shelter_end);
            // Create shelter log
            $this->logService->createLog(ActionType::OPEN->value, Shelter::class, $shelter->id, Shelter::class, $shelter->id);
        } else {
            $notification = $this->notificationService->createNotification('shelter.closed', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
            $this->notificationService->sendToAllUsers($notification);
            (new LineController)->LineNotification($line_data->toArray(request()), LineController::Shelter_post);
            // Create shelter log
            $this->logService->createLog(ActionType::CLOSE->value, Shelter::class, $shelter->id, Shelter::class, $shelter->id);
        }

        // Create a line notification
        (new LineController)->LineNotification($line_data->toArray(request()), LineController::Shelter_post);
    }

    /**
     * Handle the ShelterStaff "updated" event.
     */
    public function updated(Shelter $shelter): void
    {
        $newData = $shelter->getAttributes();
        $line_data = new ShelterResource($shelter);

        if ($shelter->wasChanged('status')) {
            if ($newData['status'] == Shelter::OPEN) {
                // Send an opened shelter notification
                $notification = $this->notificationService->createNotification('shelter.opened', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
                $this->notificationService->sendToAllUsers($notification);
                (new LineController)->LineNotification($line_data->toArray(request()), LineController::Shelter_post);
                // Create shelter log
                $this->logService->createLog(ActionType::CLOSE->value, Shelter::class, $shelter->id, Shelter::class, $shelter->id);
            } else {
                app(ShelterPopulationService::class)->deleteByShelter(['shelter_id' => $shelter->id]);
                app(AppointmentService::class)->deleteUpcomingAppointment(['shelter_id' => $shelter->id]);
                (new ShelterProductController)->transfer($shelter->id);
                (new ShelterProductController)->delete($shelter->id);
                // Send a closed shelter notification
                $notification = $this->notificationService->createNotification('shelter.closed', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
                $this->notificationService->sendToAllUsers($notification);
                (new LineController)->LineNotification($line_data->toArray(request()), LineController::Shelter_end);
                // Create shelter log
                $this->logService->createLog(ActionType::OPEN->value, Shelter::class, $shelter->id, Shelter::class, $shelter->id);
            }
        }
    }
}
