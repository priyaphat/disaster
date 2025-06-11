<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentLog extends Model
{
    //
    protected $fillable = ['parentable_id', 'parentable_type', 'description', 'log_date'];
    protected $casts = [
        'description' => "array"
    ];
    
    public function parent()
    {
        return $this->morphTo('parentable');  // เชื่อมกับ parentable_id, parentable_type
    }
}
