<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentLogs extends Model
{
    public $fillable = [
        'assessment_id',
        'name_of_assessment',
        'datetime',
        'created_by',
        'created_at'
    ];

    public function assessment()
    {
         return $this->belongsTo(Assessments::class, 'assessment_id', 'id');
    }
}
