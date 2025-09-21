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
        'role',
        'latitude',
        'longitude',
        'province'
    ];

    public function staff()
    {
        return $this->hasOneThrough(
            ShelterStaff::class,   // Final model
            User::class,   // Intermediate model
            'id',  // Foreign key on first model
            'user_id',    // Foreign key on Intermediate model
            'user_id',          // Local key on Final model
            'id'           // Local key on Intermediate model
        );
    }


    public function team()
    {
        return $this->hasOneThrough(
            TeamMember::class,   // Final model
            User::class,   // Intermediate model
            'id',  // Foreign key on first model
            'user_id',    // Foreign key on Intermediate model
            'user_id',          // Local key on Final model
            'id'           // Local key on Intermediate model
        );
    }
}
