<?php

namespace App\Enums;

enum TeamPosition: int
{
    //
    case LEADER = 1;
    case MEMBER = 2;

    public function label(): string
    {
        return match ($this) {
            self::LEADER => 'หัวหน้าทีม',
            self::MEMBER => 'สมาชิกในทีม',
        };
    }
}
