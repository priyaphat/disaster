<?php

namespace App\Http\Resources;

use App\Enums\TeamPosition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    protected $teamName;

    public function __construct($resource, $teamName = 'team')
    {
        parent::__construct($resource);
        $this->teamName = $teamName;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prefix_name' => $this->prefix?->name,
            'firstname' =>  $this->firstname,
            'lastname' =>  $this->lastname,
            'fullname' =>  $this->fullname,
            'phone' =>  $this->phone,
            'email' =>  $this->email,
            'phone' =>  $this->phone,
            'type' => $this->type->value,
            'type_name' => $this->type->label(),
            'province_code' => $this->area?->province_code,
            'province_name_th' => $this->area?->province_name_th,
            'province_name_en' => $this->area?->province_name_en,
            'district_code' => $this->area?->district_code,
            'district_name_th' => $this->area?->district_name_th,
            'district_name_en' => $this->area?->district_name_en,
            'department_id' => $this->department_id,
            'department_name' => $this->department?->name,
            'position_id' => $this->position_id,
            'position_name' => $this->position?->name,
            'registration_type' => $this->registration_type,
            'team_name' => $this->teamName,
            'team_position' =>  $this->when($this->team_position, $this->team_position),
            'team_position_name' =>  $this->when($this->team_position, $this->team_position ? TeamPosition::from($this->team_position)->label() : null),
            'position_name' => $this->position->name,
            'image' => $this->image?->image_path,
            'default_image' => $this->image?->image_name === 'default.png' ? true : false,
        ];
    }
}
