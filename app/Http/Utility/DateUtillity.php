<?php

namespace App\Http\Utility;

use Carbon\Carbon;

class DateUtillity
{
    public static function toThaiDate($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Bangkok')->addYear(543)->format('d-m-Y');
    }

    public static function toThaiSlashDate($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Bangkok')->addYear(543)->format('d/m/Y');
    }

    public static function toAD($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Bangkok')->format('Y-m-d');
    }

    public static function thaiToAD($value)
    {
        return Carbon::createFromFormat('d/m/Y', $value)->setTimezone('Asia/Bangkok')->subYear(543)->format('Y-m-d');
    }

    public static function toThaiDateTime($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Bangkok')->addYear(543)->format('d-m-Y H:i:s');
    }

    public static function createAD($value)
    {
        return Carbon::createFromFormat('d/m/Y', $value)->setTimezone('Asia/Bangkok')->subYear(543);
    }

}
