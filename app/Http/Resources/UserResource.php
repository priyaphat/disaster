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
            'prefix_name' => $this->when($this->relationLoaded('prefix'), fn() => $this->prefix?->name),
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'fullname' => $this->when($this->fullname, $this->fullname),
            'phone' => $this->phone,
            'email' => $this->email,
            'type' => $this->type->value,
            'type_name' => $this->type->label(),
            'area' => $this->when($this->relationLoaded('area'), function (){
                return [
                    'province_code' => $this->area?->province_code,
                    'province_name_th' => $this->area?->province_name_th,
                    'province_name_en' => $this->area?->province_name_en,
                    'district_code' => $this->area?->district_code,
                    'district_name_th' => $this->area?->district_name_th,
                    'district_name_en' => $this->area?->district_name_en,
                ];
            }),
            'department_id' => $this->department_id,
            'department_name' => $this->when($this->relationLoaded('department'), fn() => $this->department?->name),
            'position_id' => $this->position_id,
            'position_name' => $this->when($this->relationLoaded('position'), fn() => $this->position?->name),
            'registration_type' => $this->registration_type,
            'image' =>  $this->when($this->image, $this->image?->image_path),
            'team_id' => $this->when($this->relationLoaded('member'), fn() => $this->member?->team?->id),
            'team_name' => $this->when($this->relationLoaded('member'), fn() => $this->member?->team?->name),
            'shelter_id' =>  $this->when($this->relationLoaded('staff'), fn() => $this->staff?->shelter?->id),
            'shelter_name' =>  $this->when($this->relationLoaded('staff'), fn() => $this->staff?->shelter?->name),
            'shelter_position_id' =>  $this->when($this->relationLoaded('staff'), fn() => $this->staff?->shelterPosition?->id),
            'shelter_position_name' =>  $this->when($this->relationLoaded('staff'), fn() => $this->staff?->shelterPosition?->name),
        ];
    }
}
