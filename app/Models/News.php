<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use CreatedUpdatedBy;

    public const IMAGE_FOLDER = 'news';

    protected $fillable = [
        'title',
        'description',
    ];

     public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }
}
