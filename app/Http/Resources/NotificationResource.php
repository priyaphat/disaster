<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'notification' => $this->notification->notification,
            'entity_id' => $this->notification->entity_id,
            'entity_type' => $this->notification->entity_type,
            'show_detail' => $this->notification->show_details,
            'is_read' => $this->is_read,
        ];
    }
}
