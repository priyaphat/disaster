<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopulationResource extends JsonResource
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
            'name' => $this->prefix->name . ' ' . $this->first_name . ' ' . $this->last_name,
            'full_c_address' => $this->c_address . ' ต.' . $this->fullcaddress->subdistrict_name_th . ' อ.' . $this->fullcaddress->district_name_th . ' จ.' . $this->fullcaddress->province_name_th,
            // 'full_p_address' => $this->p_address . ' ต.' . $this->fullpaddress->subdistrict_name_th . ' อ.' . $this->fullpaddress->district_name_th . ' จ.' . $this->fullpaddress->province_name_th,
            'gender' => $this->gender,
            'status' => $this->status,
        ];
    }
}
