<?php

namespace App\Http\Resources;

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
            'full_p_address_th' => $this->fullpaddress ? 'ต.' . $this->fullpaddress->subdistrict_name_th . ' อ.'
                . $this->fullpaddress->district_name_th . ' จ.' . $this->fullpaddress->province_name_th : null,
            'full_p_address_en' => $this->fullpaddress ? 'Tambon ' . $this->fullpaddress->subdistrict_name_en . ' Amphoe'
                . $this->fullpaddress->district_name_en . ' ' . $this->fullpaddress->province_name_en : null,
            'p_address' => $this->p_address ?? '',
            'p_geography_id' => $this->fullpaddress ? $this->fullpaddress->id : null,
            'p_province_code' => $this->fullpaddress ? $this->fullpaddress->province_code : null,
            'p_province_name_th' => $this->fullpaddress ? $this->fullpaddress->province_name_th : null,
            'p_district_code' => $this->fullpaddress ? $this->fullpaddress->district_code : null,
            'p_district_name_th' => $this->fullpaddress ? $this->fullpaddress->district_name_th : null,
            'p_subdistrict_code' => $this->fullpaddress ? $this->fullpaddress->subdistrict_code : null,
            'p_subdistrict_name_th' => $this->fullpaddress ? $this->fullpaddress->subdistrict_name_th : null,
            'p_postal_code' => $this->fullpaddress ? $this->fullpaddress->postal_code : null,

            'full_c_address_th' => $this->fullcaddress ? 'ต.' . $this->fullcaddress->subdistrict_name_th . ' อ.' 
            . $this->fullcaddress->district_name_th . ' จ.' . $this->fullcaddress->province_name_th : null,
            'full_c_address_en' => $this->fullcaddress ? 'Tambon ' . $this->fullcaddress->subdistrict_name_en . ' Amphoe ' 
            . $this->fullcaddress->district_name_en . ' ' . $this->fullcaddress->province_name_en : null,
            'c_address' => $this->c_address ?? '',
            'c_geography_id' => $this->fullcaddress ? $this->fullcaddress->id : null,
            'c_province_code' => $this->fullcaddress ? $this->fullcaddress->province_code : null,
            'c_province_name_th' => $this->fullcaddress ? $this->fullcaddress->province_name_th : null,
            'c_district_code' => $this->fullcaddress ? $this->fullcaddress->district_code : null,
            'c_district_name_th' => $this->fullcaddress ? $this->fullcaddress->district_name_th : null,
            'c_subdistrict_code' => $this->fullcaddress ? $this->fullcaddress->subdistrict_code : null,
            'c_subdistrict_name_th' => $this->fullcaddress ? $this->fullcaddress->subdistrict_name_th : null,
            'c_postal_code' => $this->fullcaddress ? $this->fullcaddress->postal_code : null,
            'same_address' => $this->same_address,
            'shelter_id' => $this->shelter ? $this->shelter->id : null,
            'shelter_name' => $this->shelter ? $this->shelter->name : null,
            'created_date' => Carbon::parse($this->created_date)->format('Y-m-d'),
            'gender' => $this->gender,
            'status' => $this->status,
            'tracking' => $this->tracking,
            'image' =>  $this->when($this->image, $this->image?->image_path),
            'has_shelter' => $this->shelter ? 1 : 0,
        ];
    }
}
