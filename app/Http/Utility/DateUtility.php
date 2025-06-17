<?php

namespace App\Http\Utility;

use Carbon\Carbon;

class DateUtility
{

    public static function createFromFormat($value)
    {
       return  Carbon::createFromFormat('m-Y', $value);
    }
}
