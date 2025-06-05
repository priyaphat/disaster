<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MemberCollection extends ResourceCollection
{
    protected $teamName;

    public function __construct($resource, $teamName = null)
    {
        parent::__construct($resource);
        $this->teamName = $teamName;
    }

    public function toArray($request)
    {
       return $this->collection->map(fn ($player) => new MemberResource($player, $this->teamName));
    }
}
