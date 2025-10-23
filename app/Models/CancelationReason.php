<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CancelationReason extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'name',
        'status',
    ];

    public function reason(): HasMany
    {
        return $this->hasMany(EmergencyRequestLog::class, 'reason_id')
                    ->select(['id', 'reason_id']);
    }
}
