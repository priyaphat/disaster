<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUser extends Model
{
    public const User = 1;
    public const Population = 2;

    protected $fillable = [
        'user_id',
        'token_id',
        'role'
    ];
}
