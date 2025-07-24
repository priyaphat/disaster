<?php

namespace App\Enums;

enum SmogStatus: int
{
    case NORMAL = 1;
    case HIGH = 2;
    case OVER_FLOW = 3;

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'ปกติ',
            self::HIGH => 'มาก',
            self::OVER_FLOW => 'อันตราย',
        };
    }
}
