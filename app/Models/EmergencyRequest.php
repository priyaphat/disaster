<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'prefix_id',
        'first_name',
        'last_name',
        'phone',
        'range_age',
        'victim_name',
        'help_id',
        'description',
        'tambon',
        'address',
        'status',
        'emergency_team_id'
    ];

    // public function prefix()
    // {
    //     return $this->belongsTo(Prefix::class);
    // }

    // public function help()
    // {
    //     return $this->belongsTo(Help::class);
    // }

    // public function emergencyTeam()
    // {
    //     return $this->belongsTo(EmergencyTeam::class);
    // }

}
