<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserApproval extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'user_id',
        'is_approved',
        'approved_by',
        'is_approved',
        'approved_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
