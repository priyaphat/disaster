<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'name'
    ];
}
