<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'prefix_id' => $this->prefix_id,
            'prefix_name' => $this->prefix->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->prefix->name . ' ' . $this->first_name . ' ' . $this->last_name,
            'birth_date' => Carbon::parse($this->birth_date)->format('Y-m-d'),
            'age' => (int) $this->birth_date->diffInYears(now()),
            'phone' => $this->phone,
            'full_p_address_th' => 'ต.' . $this->fullpaddress->subdistrict_name_th . ' อ.' . $this->fullpaddress->district_name_th . ' จ.' . $this->fullpaddress->province_name_th,
            'full_p_address_en' => 'Tambon ' . $this->fullpaddress->subdistrict_name_en . ' Amphoe' . $this->fullpaddress->district_name_en . ' ' . $this->fullpaddress->province_name_en,
            'p_address' => $this->p_address,
            'p_geography_id' => $this->fullcaddress->id,
            'p_province_code' => $this->fullpaddress->province_code,
            'p_province_name_th' => $this->fullpaddress->province_name_th,
            'p_district_code' => $this->fullpaddress->district_code,
            'p_district_name_th' => $this->fullpaddress->district_name_th,
            'p_subdistrict_code' => $this->fullpaddress->subdistrict_code,
            'p_subdistrict_name_th' => $this->fullpaddress->subdistrict_name_th,
            'p_postal_code' => $this->fullpaddress->postal_code,
            'full_c_address_th' => 'ต.' . $this->fullcaddress->subdistrict_name_th . ' อ.' . $this->fullcaddress->district_name_th . ' จ.' . $this->fullcaddress->province_name_th,
            'full_c_address_en' => 'Tambon ' . $this->fullcaddress->subdistrict_name_en . ' Amphoe ' . $this->fullcaddress->district_name_en . ' ' . $this->fullcaddress->province_name_en,
            'c_address' => $this->c_address,
            'c_geography_id' => $this->fullcaddress->id,
            'c_province_code' => $this->fullcaddress->province_code,
            'c_province_name_th' => $this->fullcaddress->province_name_th,
            'c_district_code' => $this->fullcaddress->district_code,
            'c_district_name_th' => $this->fullcaddress->district_name_th,
            'c_subdistrict_code' => $this->fullcaddress->subdistrict_code,
            'c_subdistrict_name_th' => $this->fullcaddress->subdistrict_name_th,
            'c_postal_code' => $this->fullcaddress->postal_code,
            'same_address' => $this->same_address,
            'shelter_id' => $this->shelter ? $this->shelter->id : null,
            'shelter_name' => $this->shelter ? $this->shelter->name : null,
            'created_date' => Carbon::parse($this->created_date)->format('Y-m-d'),
            'gender' => $this->gender,
            'status' => $this->status,
            'tracking' => $this->tracking,
            'image' =>  $this->when($this->image, $this->image?->image_path),
        ];
    }
}
