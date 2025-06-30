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
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['image_name'] === 'default.png') {
                    switch ($attributes['parentable_type']) {
                        case Shelter::class:
                            $url = Storage::disk('public')->url('shelter_default.png');
                            break;
                        default:
                            $url = Storage::disk('public')->url('user_default.png');
                    }
                    return $url;
                } elseif ($attributes['image_type'] === self::PUBLIC) {
                    return Storage::disk('public')->url($this->image_url);
                } else {
                    return url('api/' . $this->image_url);
                }
            },
        );
    }
}
