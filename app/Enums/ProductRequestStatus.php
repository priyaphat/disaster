<?php

namespace App\Enums;

enum RegistrationType: string
{
    //
    case CANCELED = 0;
    case PENDING = 1;
    case APPROVED = 2;

    public function label(): string
    {
        return match ($this) {
            self::CANCELED => 'ยุติคำขอ',
            self::PENDING => 'รออนุมัติ',
            self::APPROVED => 'อนุมัติ'
        };
    }
}
