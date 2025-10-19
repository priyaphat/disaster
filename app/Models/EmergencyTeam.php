<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyTeam extends Model
{
    protected $fillable = [
        'emc_id',
        'team_id',
        'seq_no',
    ];
    
}
