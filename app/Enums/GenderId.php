<?php

namespace App\Enums;

enum GenderId: int
{
    case UNKNOWN = 0;
    case MALE = 1;
    case FEMALE = 2;

    public static function label(int $id): self
    {
        return match ($id) {
            1, 4 => self::MALE,
            2, 3, 5 => self::FEMALE,
            default => self::UNKNOWN,
        };
    }

    public function text(): string
    {
        return match ($this) {
            self::UNKNOWN => 'ไม่ทราบ',
            self::MALE => 'ชาย',
            self::FEMALE => 'หญิง'
        };
    }
}
