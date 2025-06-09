<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentLog extends Model
{
    //
    protected $fillable = ['parent_id', 'parent_type', 'description', 'log_date'];
    protected $casts = [
        'description' => "array"
    ];
    
    public function parent()
    {
        return $this->morphTo('parent');  // เชื่อมกับ parent_id, parent_type
    }
}
