<?php

namespace App\Enums;

enum UserType: int
{
    //
    case INSIDE_AREA = 1;
    case OUTSIDE_AREA = 2;

    public function label(): string
    {
        return match ($this) {
            self::INSIDE_AREA => 'ในพื้นที่',
            self::OUTSIDE_AREA => 'นอกพื้นที่',
        };
    }
}
