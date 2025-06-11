<?php

namespace App\Enums;

enum ShiftType: int
{
    //
    case MORNING = 1;
    case EVENING = 2;

    public function label(): string
    {
        return match ($this) {
            self::MORNING => __('data.morning'),
            self::EVENING => __('data.evening'),
        };
    }
}
