<?php

namespace App\Enums;

enum UserStatus: int
{
    //
    case ACTIVE = 1;
    case INACTIVE = 0;

    public static function fromName(string $name): ?self
    {
        return match (strtoupper($name)) {
            'ACTIVE' => self::ACTIVE,
            'INACTIVE' => self::INACTIVE,
            default => null,
        };
    }
}
