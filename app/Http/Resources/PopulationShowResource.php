<?php

namespace App\Http\Resources;

use App\Http\Utility\AddressUtility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PopulationShowResource extends JsonResource
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
            'full_p_address_th' => AddressUtility::getFullAddressTH($this->p_address, $this->permanentAddress),
            'full_p_address_en' => AddressUtility::getFullAddressEN($this->p_address, $this->permanentAddress),
            'p_address' => $this->p_address,
            'p_geography_id' => $this->currentAddress->id,
            'p_province_code' => $this->permanentAddress->province_code,
            'p_province_name_th' => $this->permanentAddress->province_name_th,
            'p_district_code' => $this->permanentAddress->district_code,
            'p_district_name_th' => $this->permanentAddress->district_name_th,
            'p_subdistrict_code' => $this->permanentAddress->subdistrict_code,
            'p_subdistrict_name_th' => $this->permanentAddress->subdistrict_name_th,
            'p_postal_code' => $this->permanentAddress->postal_code,
            'full_c_address_th' => AddressUtility::getFullAddressTH($this->c_address, $this->currentAddress),
            'full_c_address_en' => AddressUtility::getFullAddressEN($this->c_address, $this->currentAddress),
            'c_address' => $this->c_address,
            'c_geography_id' => $this->currentAddress->id,
            'c_province_code' => $this->currentAddress->province_code,
            'c_province_name_th' => $this->currentAddress->province_name_th,
            'c_district_code' => $this->currentAddress->district_code,
            'c_district_name_th' => $this->currentAddress->district_name_th,
            'c_subdistrict_code' => $this->currentAddress->subdistrict_code,
            'c_subdistrict_name_th' => $this->currentAddress->subdistrict_name_th,
            'c_postal_code' => $this->currentAddress->postal_code,
            'same_address' => $this->same_address,
            'shelter_id' => $this->shelter ? $this->shelter->id : null,
            'shelter_name' => $this->shelter ? $this->shelter->name : null,
            'created_date' => Carbon::parse($this->created_date)->format('Y-m-d'),
            'gender' => $this->gender,
            'status' => $this->status,
            'tracking' => $this->tracking,
            'image' => $this->image?->image_path,
            'has_shelter' => $this->shelter ? 1 : 0,
        ];
    }
}
