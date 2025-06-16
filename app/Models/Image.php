<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    //
    protected $appends = ['image_path'];

    protected $fillable = [
        'image_name',
        'image_url',
        'parentable_type',
        'parentable_id',
    ];

    public function parent()
    {
        return $this->morphTo();
    }

    protected function imagePath(): Attribute
    {
        return new Attribute(
            get: fn() => Storage::disk('public')->url($this->image_url)
        );
    }
}
