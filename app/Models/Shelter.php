<?php

namespace App\Models;

use App\Casts\UtilitiesCast;
use App\Constants\ShelterType;
use App\Filters\ShelterFilter;
use App\Http\Traits\CreatedUpdatedBy;
use App\Http\Utility\ShelterUtility;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

class Shelter extends Model
{
    use CreatedUpdatedBy, LogsActivity, HasFactory;

    public const IMAGE_FOLDER = 'shelter';
    public const OPEN = 1;
    public const CLOSED = 2;

    // protected $appends = ['gis_map', 'google_maps'];
    // protected $appends = ['gis_map', 'google_maps'];

    protected $fillable = [
        'name',
        'phone',
        'created_date',
        'status',
        'utilities',
        'geography_id',
        'province_code',
        'district_code',
        'subdistrict_code',
        'address',
        'max_capacity',
        'shelter_type',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'status' => 'integer',
        'shelter_type' => 'integer',
        'utilities' => UtilitiesCast::class,
    ];

    protected function originalName(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['name'],
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (isset($attributes['shelter_type'])) {
                    $type = ShelterType::findById((int)$attributes['shelter_type']);
                    if (!$type || !isset($type['prefixes'])) {
                        return $value;
                    }
                    return Str::of($type['name'])->append(' ')->append($value);
                }
                return $value;
            }
        );
    }

    protected function gisMap(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return ShelterUtility::getGisMap($attributes['shelter_type'], $attributes['name']);
            }
        );
    }

    protected function googleMaps(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => env('GOOGLE_MAPS_URL') . '/' . urlencode($attributes['name']),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('shelters')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }

    public function fullAddress()
    {
        return $this->belongsTo(Geography::class, 'geography_id')->select('id', 'province_name_th', 'district_name_th', 'subdistrict_name_th');
    }

    public function geography()
    {
        return $this->belongsTo(Geography::class, 'geography_id')->select('id', 'province_code', 'province_name_th', 'province_name_en', 'district_code', 'district_name_th', 'district_name_en', 'subdistrict_code', 'subdistrict_name_th', 'subdistrict_name_en', 'postal_code');
    }

    public function leader()
    {
        return $this->hasOneThrough(
            User::class,   // Final model
            ShelterStaff::class, // Intermediate model
            'shelter_id',
            'id',
            'id',
            'user_id'
        )->where('shelter_staffs.shelter_position_id', 1);
    }

    public function populations()
    {
        return $this->hasMany(ShelterPopulation::class, 'shelter_id');
    }

    public function staff()
    {
        return $this->hasMany(ShelterStaff::class, 'shelter_id');
    }

    public function histories()
    {
        return $this->morphMany(TransferHistory::class, 'childable');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new ShelterFilter($filters))->apply($query);
    }
}
