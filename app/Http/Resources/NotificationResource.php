<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use App\Models\News;
use App\Models\Shelter;
use App\Models\ShelterStaff;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NotificationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $entityClass = $this->entity_type;
        $entity = $entityClass::find($this->entity_id);

        $isShow = $this->show_details;
        if ($isShow) {
            $isShow = Gate::forUser(Auth::user())->allows('view', $entity);
        }

        $data = [];
        if ($entityClass === News::class) {
            $data['shelter_id'] = $this->entity?->shelter_id;
            $data['shelter_name'] = $this->entity?->shelter?->name;
            $data['entity_id'] = $this->entity?->id;
            $data['entity_name'] = $this->entity?->title;
        }
        else if ($entityClass === ShelterStaff::class) {
            $data['shelter_id'] = $this->entity?->shelter_id;
            $data['shelter_name'] = $this->entity?->shelter?->name;
        }else if ($entityClass === Appointment::class) {
            $data['shelter_id'] = $this->entity?->shelter_id;
            $data['shelter_name'] = $this->entity?->shelter?->name;
            $data['team_id'] = $this->entity?->team_id;
            $data['team_name'] = $this->entity?->team?->name;
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
            'related_data' => $data,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
        ];
    }
}
