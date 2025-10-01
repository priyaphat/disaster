<?php

namespace App\Http\Resources;

use App\Enums\ShelterUtilities;
use Carbon\Carbon;
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
            'phone' => $this->phone,
            'status' => $this->status,
            'created_date' => Carbon::parse($this->created_date)->format('Y-m-d'),
            'leader_id' => $this->leader ? $this->leader->id : null,
            'leader_name' => $this->leader ? $this->leader->prefix->name . ' ' . $this->leader->firstname . ' ' . $this->leader->lastname : null,
            'address' => $this->address,
            'geography_id' => $this->geography->id,
            'province_code' => $this->geography->province_code,
            'province_name' => $this->geography->province_name_th,
            'district_code' => $this->geography->district_code,
            'district_name' => $this->geography->district_name_th,
            'subdistrict_code' => $this->geography->subdistrict_code,
            'subdistrict_name' => $this->geography->subdistrict_name_th,
            'postal_code' => $this->geography->postal_code,
            'staff_count' => $this->staff->count(),
            'current_capacity' => $this->populations->count(),
            'max_capacity' => $this->max_capacity,
            'utilities' => $this->utilities,
            'image' =>  $this->when($this->image, $this->image?->image_path),
            'gis_map' => $this->gis_map ?? null,
            'google_maps' => $this->google_maps ?? null,
        ];
    }
}
