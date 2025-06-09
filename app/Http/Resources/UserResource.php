<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'prefix_name' => $this->when($this->relationLoaded('prefix'), $this->prefix?->name),
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'type' => $this->type->value,
            'type_name' => $this->type->label(),
            'area' => $this->when($this->relationLoaded('area'), function (){
                return [
                    'province_name_th' => $this->area?->province_name_th,
                    'province_name_en' => $this->area?->province_name_en,
                    'district_name_th' => $this->area?->district_name_th,
                    'district_name_en' => $this->area?->district_name_en,
                ];
            }),
            'department_id' => $this->department_id,
            'department_name' => $this->when($this->relationLoaded('department'), $this->department?->name),
            'position_id' => $this->position_id,
            'position_name' => $this->when($this->relationLoaded('position'), $this->position?->name),
            'registration_type' => $this->registration_type->label(),
            'team_name' => $this->when($this->relationLoaded('member'), $this->member?->team?->name),
            'image' =>  $this->when($this->relationLoaded('image'), $this->image?->image_url),
        ];
    }
}
