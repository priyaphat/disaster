<?php

namespace App\Models;

use App\Filters\ShelterFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Shelter extends Model
{
    use CreatedUpdatedBy, LogsActivity, HasFactory;

    public const IMAGE_FOLDER = 'shelter';
    public const OPEN = 1;
    public const CLOSED = 2;

    protected $appends = ['gis_map', 'google_maps'];

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
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'status' => 'integer',
        'utilities' => 'array',
    ];


    protected function gisMap(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => env('GIS_SHELTER_URL') . '?' . http_build_query(['name' => $attributes['name']]),
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
