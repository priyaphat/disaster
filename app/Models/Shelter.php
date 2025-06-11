<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Shelter extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    public const IMAGE_FOLDER = 'shelter';
    public const OPEN = 1;
    public const CLOSED = 0;

    protected $fillable = [
        'name',
        'phone',
        'created_date',
        'status',
        'leader_id',
        'utilities',
        'geography_id',
        'province_code',
        'district_code',
        'subdistrict_code',
        'address',
        'max_capacity',
    ];

    protected $casts = [
        'status' => 'integer',
        'utilities' => 'array',
    ];

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

    public function fulladdress() {
        return $this->belongsTo(Geography::class, 'geography_id')->select('id', 'province_name_th', 'district_name_th', 'subdistrict_name_th');
    }

    public function geography()
    {
        return $this->belongsTo(Geography::class, 'geography_id')->select('id', 'province_code', 'province_name_th', 'province_name_en', 'district_code', 'district_name_th', 'district_name_en', 'subdistrict_code', 'subdistrict_name_th', 'subdistrict_name_en', 'postal_code');
    }

    public function countpopulation() {
        return $this->hasMany(Population::class, 'shelter_id');
    }

    public function leader() {
        return $this->belongsTo(User::class, 'leader_id')->select('id', 'firstname', 'lastname');
    }

    public function countstaff() {
        return $this->hasMany(User::class, 'id');
    }
}
