<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read'
    ];

     public function notification() {
        return $this->hasOne(Notification::class, 'id', 'notification_id');
    }
}
