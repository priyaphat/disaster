<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShelterStaffResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user?->id,
            'prefix_id' => $this->user?->prefix_id,
            'prefix_name' => $this->user?->prefix?->name,
            'firstname' =>  $this->user?->firstname,
            'lastname' =>  $this->user?->lastname,
            'name' => $this->user?->prefix?->name . ' ' . $this->user?->firstname . ' ' . $this->user?->lastname,
            'phone' => $this->user?->phone,
            'email' => $this->user?->email,
            'type' => $this->user?->type->value,
            'type_name' => $this->user?->type->label(),
            'area' => $this->when($this->user?->area, function (){
                return [
                    'province_name_th' => $this->user?->area?->province_name_th,
                    'province_name_en' => $this->user?->area?->province_name_en,
                    'district_name_th' => $this->user?->area?->district_name_th,
                    'district_name_en' => $this->user?->area?->district_name_en,
                ];
            }),
            'position_id' => $this->user?->position_id,
            'position_name' => $this->user?->position->name ?? null,
            'department_id' => $this->user?->department_id,
            'department_name' => $this->user?->department->name ?? null,
            'registration_type' =>  $this->user?->registration_type->label(),
            'team_id' => $this->user?->member?->team?->id,
            'team_name' => $this->user?->member?->team?->name,
            'image' =>   $this->user?->image?->image_path,
            'shelter_id' => $this->shelter ? $this->shelter->id : null,
            'shelter_name' => $this->shelter ? $this->shelter->name : null,
            'shelter_position_id' => $this->shelter_position_id ?? null,
            'shelter_position_name' => $this->shelterposition ? $this->shelterposition->name : null,
        ];
    }
}