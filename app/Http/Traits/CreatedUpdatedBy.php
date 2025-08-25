<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait CreatedUpdatedBy
{
    // Boot method to handle model events
    protected static function bootCreatedUpdatedBy()
    {
        // Listen to the 'created' event
        static::creating(function ($model) {
            // Automatically set created_by if not set
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = Auth::id();
            }
        });

        // Optionally, you can also listen to the 'saving' event
        static::saving(function ($model) {
            // Automatically set updated_by when updating{
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
