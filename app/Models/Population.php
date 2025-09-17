<?php

namespace App\Models;

use App\Filters\PopulationFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Population extends Model
{
    use CreatedUpdatedBy, LogsActivity, HasFactory;

    public const IMAGE_FOLDER = 'population';
    public const not_tracking = 0;
    public const tracking = 1;

    public const exit = null;

    //log
    public const dead = 0;
    public const injure = 1;
    public const healthy = 2;

    //emergency to shelter population
    public const new_population = 1;
    public const old_not_has_shelter = 2;
    public const old_has_shelter = 3;

    protected $appends = ['gis_map'];

    protected $fillable = [
        // 'user_id',
        'prefix_id',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'phone',
        'p_address',
        'p_geography_id',
        'c_address',
        'c_geography_id',
        'same_address',
        'status',
        'tracking',
        'sequence_id',
        'created_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'datetime',
        'gender' => 'integer',
        'same_address' => 'integer',
        'status' => 'integer',
        'create_date' => 'datetime',
    ];

    protected $hidden = ['sequence_id'];

    protected function gisMap(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['sequence_id']) ? env('GIS_POPULATION_URL') .'?'. http_build_query(['id' => $attributes['sequence_id']]) : null,
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('populations')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    // protected function pAddress(): Attribute
    // {
    //     return Attribute::get(function ($value, $attributes) {
    //         $geo = DB::table('geography')->where('id', $attributes['p_geography_id'])->firstOrFail();

    //         unset($geo->p_geography_id);
    //         return "{$value} ตำบล {$geo->subdistrict_name_th} อำเภอ {$geo->district_name_th} จังหวัด {$geo->province_name_th} {$geo->postal_code}";
    //     });
    // }

    // protected function cAddress(): Attribute
    // {
    //     return Attribute::get(function ($value, $attributes) {
    //         $geo = DB::table('geography')->where('id', $attributes['c_geography_id'])->firstOrFail();

    //         unset($geo->p_geography_id);
    //         return "{$value} ตำบล {$geo->subdistrict_name_th} อำเภอ {$geo->district_name_th} จังหวัด {$geo->province_name_th} {$geo->postal_code}";
    //     });
    // }


    // public function getPAddressAttribute($value)
    // {
    //     $geo = DB::table('geography')->where('id', $this->p_geography_id)->firstOrFail();

    //     return "{$value} ตำบล {$geo->subdistrict_name_th} อำเภอ {$geo->district_name_th} จังหวัด {$geo->province_name_th} {$geo->postal_code}";
    // }

    // public function getCAddressAttribute($value)
    // {
    //     $geo = DB::table('geography')->where('id', $this->c_geography_id)->firstOrFail();

    //     return "{$value} ตำบล {$geo->subdistrict_name_th} อำเภอ {$geo->district_name_th} จังหวัด {$geo->province_name_th} {$geo->postal_code}";
    // }

    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }

    public function permanentAddress()
    {
        return $this->belongsTo(Geography::class, 'p_geography_id')->select('id', 'province_code', 'province_name_th', 'province_name_en', 'district_code', 'district_name_th', 'district_name_en', 'subdistrict_code', 'subdistrict_name_th', 'subdistrict_name_en', 'postal_code');
    }

    public function currentAddress()
    {
        return $this->belongsTo(Geography::class, 'c_geography_id')->select('id', 'province_code', 'province_name_th', 'province_name_en', 'district_code', 'district_name_th', 'district_name_en', 'subdistrict_code', 'subdistrict_name_th', 'subdistrict_name_en', 'postal_code');
    }

    public function shelter()
    {
        return $this->hasOneThrough(
            Shelter::class, // Final model
            ShelterPopulation::class, // Intermediate model
            'population_id', // Foreign key on first model
            'id', // Foreign key on Intermediate model
            'id', // Local key on Final model
            'shelter_id' // Local key on Intermediate model
        );
    }

    public function shelterPopulation(): HasOne
    {
        return $this->hasOne(ShelterPopulation::class, 'population_id', 'id');
    }

    public function prefix()
    {
        return $this->belongsTo(Prefix::class, 'prefix_id');
    }

    public function logs()
    {
        return $this->morphMany(TransferHistory::class, 'parentable');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new PopulationFilter($filters))->apply($query);
    }
}
