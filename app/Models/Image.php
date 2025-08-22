<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Self_;

class Image extends Model
{
    //
    public const PUBLIC = 'public';
    public const PRIVATE = 'local';
    public const DEFAULT = 'default.png';

    protected $appends = ['image_path'];

    protected $fillable = [
        'original_name',
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
                if ($attributes['image_name'] === self::DEFAULT) {
                    switch ($attributes['parentable_type']) {
                        case Shelter::class:
                            $url = Storage::disk('public')->url('shelter_default.png');
                            break;
                        case Product::class:
                            $url = Storage::disk('public')->url('product_default.png');
                            break;
                        case News::class:
                            $url = Storage::disk('public')->url('news_default.png');
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
