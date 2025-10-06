<?php

namespace App\Http\Resources;

use App\Enums\DeathCause;
use App\Http\Utility\AddressUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function PHPUnit\Framework\isNull;

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
            'full_p_address_th' => AddressUtility::getFullAddressTH($this->p_address, $this->permanentAddress) ?? '-',
            'full_p_address_en' => AddressUtility::getFullAddressEN($this->p_address, $this->permanentAddress) ?? '-',
            'p_address' => $this->p_address ?? '',
            'p_geography_id' => $this->permanentAddress ? $this->permanentAddress->id : null,
            'p_province_code' => $this->permanentAddress ? $this->permanentAddress->province_code : null,
            'p_province_name_th' => $this->permanentAddress ? $this->permanentAddress->province_name_th : null,
            'p_district_code' => $this->permanentAddress ? $this->permanentAddress->district_code : null,
            'p_district_name_th' => $this->permanentAddress ? $this->permanentAddress->district_name_th : null,
            'p_subdistrict_code' => $this->permanentAddress ? $this->permanentAddress->subdistrict_code : null,
            'p_subdistrict_name_th' => $this->permanentAddress ? $this->permanentAddress->subdistrict_name_th : null,
            'p_postal_code' => $this->permanentAddress ? $this->permanentAddress->postal_code : null,
            'full_c_address_th' => AddressUtility::getFullAddressTH($this->c_address, $this->currentAddress),
            'full_c_address_en' => AddressUtility::getFullAddressEN($this->c_address, $this->currentAddress),
            'c_address' => $this->c_address ?? '',
            'c_geography_id' => $this->currentAddress ? $this->currentAddress->id : null,
            'c_province_code' => $this->currentAddress ? $this->currentAddress->province_code : null,
            'c_province_name_th' => $this->currentAddress ? $this->currentAddress->province_name_th : null,
            'c_district_code' => $this->currentAddress ? $this->currentAddress->district_code : null,
            'c_district_name_th' => $this->currentAddress ? $this->currentAddress->district_name_th : null,
            'c_subdistrict_code' => $this->currentAddress ? $this->currentAddress->subdistrict_code : null,
            'c_subdistrict_name_th' => $this->currentAddress ? $this->currentAddress->subdistrict_name_th : null,
            'c_postal_code' => $this->currentAddress ? $this->currentAddress->postal_code : null,
            'same_address' => $this->same_address,
            'velnerable_group_id' => $this->when($this->relationLoaded('group'),fn() => $this->group?->id),
            'velnerable_group_name' => $this->when($this->relationLoaded('group'),fn() => $this->group?->name ?? __('data.no_vulnerable')),
            'shelter_name' => $this->shelter ? $this->shelter->name : null,
            'created_date' => Carbon::parse($this->created_date)->format('Y-m-d'),
            'gender' => $this->gender,
            'status' => $this->status,
            'death_cause' => $this->death_cause,
            'death_cause_label' => !is_null($this->death_cause) ? DeathCause::tryFrom($this->death_cause)->label() : null,
            'allergy' => $this->when($this->relationLoaded('allergy'),fn() => $this->allergy),
            'tracking' => $this->tracking,
            'gis_map' => $this->gis_map,
            'image' => $this->image?->image_path,
            'has_shelter' => $this->shelter ? 1 : 0,
        ];
    }
}
