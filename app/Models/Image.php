<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    //
    public const PUBLIC = 'public';
    public const PRIVATE = 'local';

    protected $appends = ['image_path'];

    protected $fillable = [
        'image_name',
        'image_url',
        'image_type',
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
            get: fn() => $this->image_type === self::PUBLIC ?
                Storage::disk('public')->url($this->image_url) : url('api/private/' . $this->image_url)
        );
    }
}
