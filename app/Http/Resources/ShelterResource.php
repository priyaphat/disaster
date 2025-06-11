<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShelterResource extends JsonResource
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
            'name' => $this->name,
            'current_capacity' => $this->countpopulation->count(),
            'max_capacity' => $this->max_capacity,
            'address' => $this->address . ' ต.' . $this->fulladdress->subdistrict_name_th . ' อ.' . $this->fulladdress->district_name_th . ' จ.' . $this->fulladdress->province_name_th,
            'status' => $this->status,
        ];
    }
}
