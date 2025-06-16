<?php

namespace App\Models;

use App\Casts\ThaiDateTimeDisplayFormat;
use App\Casts\ThaiDateTimeFormat;
use App\Filters\NewsFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class News extends Model
{
    use CreatedUpdatedBy;

    public const IMAGE_FOLDER = 'news';
    protected $appends = ['thai_created_at'];
    protected $fillable = [
        'title',
        'description',
        'line_oa',
        'shelter_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'line_oa' => 'integer',
            'created_at' => ThaiDateTimeFormat::class,
            'updated_at' => ThaiDateTimeFormat::class,
        ];
    }

    /**
     * Count comments of posts
     */
    protected function thaiCreatedAt(): Attribute
    {
        return new Attribute(
            get: fn() => Carbon::parse($this->attributes['created_at'])->thaidate('j M y')
        );
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function editor(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new NewsFilter($filters))->apply($query);
    }
}
