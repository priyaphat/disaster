<?php

namespace App\Models;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    //
    public const ADMIN = 'admin';
    public const STAFF = 'staff';
    public const TEAM_LEADER = 'team_leader';
    public const TEAM_MEMBER = 'team_member';

}
