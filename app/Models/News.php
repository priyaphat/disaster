<?php

namespace App\Models;

use App\Filters\NewsFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;

class News extends Model
{
    use CreatedUpdatedBy, HasFactory;

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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function shelter(): HasOne
    {
        return $this->hasOne(Shelter::class, 'id', 'shelter_id');
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by')->withTrashed();
    }

    public function editor(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by')->withTrashed();
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new NewsFilter($filters))->apply($query);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('news')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
