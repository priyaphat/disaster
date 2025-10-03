<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class ShelterType extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'name'
    ];

    public function shelters()
    {
        return $this->belongsTo(Shelter::class, 'id', 'shelter_type_id');
    }
}
