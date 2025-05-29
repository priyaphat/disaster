<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RefArea extends Model
{
    // use LogsActivity;

    protected $table = 'ref_areas';
    public $timestamps = false;

    protected $fillable = [
        'district_id',
        'post_id',
    ];


    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('RefArea')
    //         ->logOnly(['district_id', 'post_id'])
    //         ->logOnlyDirty();
    // }
}
