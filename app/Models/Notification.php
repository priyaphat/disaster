<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'notification',
        'entity_id',
        'entity_type',
        'notification_type',
        'event_type',
        'event_action',
        'show_details'
    ];

    protected $casts = [
        'notification' => 'array', // หรือใช้ 'json' ก็ได้
        'show_details' => 'boolean'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function notification_users()
    {
        return $this->hasMany(NotificationUser::class, 'notification_id', 'id');
    }
}
