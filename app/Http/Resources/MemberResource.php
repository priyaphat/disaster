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
            'fullname' =>  $this->prefix?->name.$this->firstname.' '.$this->lastname,
            'phone' =>  $this->phone,
            'email' =>  $this->email,
            'phone' =>  $this->phone,
            'team_name' => $this->teamName,
            'team_position' =>  $this->when($this->team_position, $this->team_position),
            'team_position_name' =>  $this->when($this->team_position, $this->team_position ? TeamPosition::from($this->team_position)->label() : null),
            'position_name' => $this->position->name,
            'image' => $this->image?->image_url,
        ];
    }
}
