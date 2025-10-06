<?php

namespace App\Enums;

enum DeathCause: int
{
    //
    case NO = 0;
    case YES = 1;

    public function label(): string
    {
        return match ($this) {
            self::NO => 'ไม่เกี่ยวกับภัย',
            self::YES => 'เกี่ยวกับภัย'
        };
    }
}
