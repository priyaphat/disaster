<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
        'readed_at'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

     public function notification() {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }
}
