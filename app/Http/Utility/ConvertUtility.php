<?php

namespace App\Http\Utility;

class ConvertUtility
{

    public static function toInt($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return $value;
    }
}
