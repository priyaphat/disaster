<?php

namespace App\Http\Utility;

use Carbon\Carbon;

class DateUtility
{

    public static function createFromFormat($value)
    {
        return  Carbon::createFromFormat('m-Y', $value);
    }

    public static function formFormat1($val)
    {
        $date = Carbon::parse($val);
        $newYear = $date->year + 543;
        return $date->format('d/m/') . $newYear . $date->format(' H:i');
    }

    public static function time($val)
    {
        $date = Carbon::parse($val);
        return $date->format('H:i:m');
    }

    public static function months($date) //14 กรกฎาคม
    {
        $date = Carbon::parse($date);
        $dateText = $date->locale('th')->isoFormat('D MMMM');
        return $dateText;
    }

    public static function days($date) // จันทร์
    {
        $date = Carbon::parse($date);
        $day = $date->locale('th')->isoFormat('dddd');
        return $day;
    }
}
