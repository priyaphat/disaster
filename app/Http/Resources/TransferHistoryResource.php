<?php

namespace App\Http\Resources;

use App\Enums\ActionType;
use App\Models\Population;
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
        $object = $this->childable?->name ?? $this->childable_name;
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'action_type' => ActionType::from($this->action_type)->label($this->parentable_type),
            'object' => $object,
            'description' => ActionType::from($this->action_type)->label($this->parentable_type).' '. $object,
            'created_by' => $this->created_by_name->firstname . ' ' . $this->created_by_name->lastname
        ];
    }
}
