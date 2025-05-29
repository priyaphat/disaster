<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyPostLog extends Model
{

    protected $table = 'emergency_post_logs';
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'disaster_id',
        'action',
        'description',
        'district',
        'created_at',
        'created_by'
    ];



}
