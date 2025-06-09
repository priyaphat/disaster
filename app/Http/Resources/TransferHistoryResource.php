<?php

namespace App\Http\Resources;

use App\Enums\ActionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferHistoryResource extends JsonResource
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
            'created_at' => $this->created_at,
            'action_type' => ActionType::from($this->action_type)->action_type(),
            'team_shelter' => $this->shelter->name ?? $this->team->name,
            'created_by' => $this->created_by_name->firstname . ' ' . $this->created_by_name->lastname
        ];
    }
}
