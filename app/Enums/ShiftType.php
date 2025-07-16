<?php

namespace App\Enums;

enum ShiftType: int
{
    //
    case MORNING = 1;
    case AFTERNOON = 2;

    public function label(): string
    {
        return match ($this) {
            self::MORNING => __('data.morning'),
            self::AFTERNOON => __('data.afternoon'),
        };
    }

    public function type(): string
    {
        return match ($this) {
            self::MORNING => 'morning',
            self::AFTERNOON => 'afternoon',
        };
    }
}
