<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use App\Models\DamagedUtility;
use App\Models\EmergencyRequest;
use App\Models\News;
use App\Models\ProductLogs;
use App\Models\ProductRequest;
use App\Models\Shelter;
use App\Models\ShelterProduct;
use App\Models\ShelterStaff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NotificationResource extends JsonResource
{
    protected $user;

    public function __construct($resource, $user = null)
    {

        parent::__construct($resource);
        $this->user = $user instanceof User ? $user : Auth::user();
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $entityClass = $this->entity_type;
        $entity = $entityClass::find($this->entity_id);
        $relatedData = [];
        $isShow = $this->show_details;
        if ($isShow) {
            $isShow = Gate::forUser($this->user)->allows('viewNoti', [$entity ?? new $entityClass, $this->resource]);
            if ($entityClass === News::class) {
                $relatedData['shelter_id'] = $this->entity?->shelter_id;
                $relatedData['shelter_name'] = $this->entity?->shelter?->name;
                $relatedData['entity_id'] = $this->entity?->id;
                $relatedData['entity_name'] = $this->entity?->title;
            } else if ($entityClass === ProductRequest::class) {
                $relatedData['entity_id'] = $this->entity_id;
                $relatedData['product_id'] = $this->entity?->product_id;
            } else if ($entityClass === ShelterStaff::class || $entityClass === ShelterProduct::class) {
                $relatedData['shelter_id'] = $this->entity?->shelter_id;
                $relatedData['shelter_name'] = $this->entity?->shelter?->name;
            } else if ($entityClass === Appointment::class) {
                $relatedData['shelter_id'] = $this->entity?->shelter_id;
                $relatedData['shelter_name'] = $this->entity?->shelter?->name;
                $relatedData['team_id'] = $this->entity?->team_id;
                $relatedData['team_name'] = $this->entity?->team?->name;
            } else if ($entityClass === ProductLogs::class) {
                $relatedData['from_shelter_id'] = $this->entity?->fromShelter?->id;
                $relatedData['from_shelter_name'] = $this->entity?->fromShelter?->name;
                $relatedData['to_shelter_id'] = $this->entity?->toShelter?->id;
                $relatedData['to_shelter_name'] = $this->entity?->toShelter?->name;
            } else if ($entityClass === User::class) {
                $relatedData['entity_id'] = $this->entity_id;
                $relatedData['entity_name'] = $this->entity?->fullname;
            } else if ($entityClass === EmergencyRequest::class) {
                $relatedData['entity_id'] = $this->entity_id;
            } else if ($entityClass === DamagedUtility::class) {
                $relatedData['type'] = $this->entity?->type;
            }
        }

        return [
            'id' => $this->id,
            'notification' => $this->notification,
            'entity_id' => $this->entity_id,
            'entity_type' => $this->entity_type,
            'notification_type' =>  $this->notification_type,
            'event_type' =>  $this->event_type ?? null,
            'event_action' =>  $this->event_action ?? null,
            'show_details' => $isShow,
            'related_data' => $relatedData,
            'is_read' => $this->notification_users[0]['is_read'] ?? null,
            'created_at' => $this->created_at,
        ];
    }
}
