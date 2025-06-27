<?php

namespace App\Http\Utility;

use Carbon\Carbon;

class DateUtility
{

    public static function createFromFormat($value)
    {
        return  Carbon::createFromFormat('m-Y', $value);
    }

    public static function th_time($date)
    {
        // $date = '2015-03-11';
        $formatted = Carbon::parse($date)->locale('th')->translatedFormat('j M Y');

        $formatted = str_replace(
            Carbon::parse($date)->year,
            Carbon::parse($date)->year + 543,
            $formatted
        );
        return $formatted;
    }

    public static function formFormat1($val)
    {
        $date = Carbon::parse($val);
        $newYear = $date->year + 543;
        return $date->format('d/m/') . $newYear . $date->format(' H:i');
    }
}
