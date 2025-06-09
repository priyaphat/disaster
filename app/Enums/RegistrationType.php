<?php

namespace App\Enums;

enum RegistrationType: string
{
    //
    case SELF = 'self';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::SELF => 'สมัครเอง',
            self::ADMIN => 'แอดมินสมัครให้',
        };
    }
}
