<?php

namespace App\Http\Utility;

use App\Models\LineUser;
use Carbon\Carbon;

class RecipintUtility
{
    public static function recipint($r)
    {
        if ($r == 'admin') {
            $q = LineUser::select('line_users.*')
                ->where('model_has_roles.model_type', 'App\Models\User')
                ->where('model_has_roles.role_id', 1) //admin
                ->where('line_users.role', LineUser::User)
                ->leftJoin('model_has_roles', 'line_users.user_id', 'model_has_roles.model_id')
                ->pluck('token_id');
            return $q;
        }
    }
}
