<?php

namespace App\Enums;

enum ProductLogs: int
{
    case TRANSFER = 1;
    case REQUEST = 2;
    case STOP = 3;
    case RECEIVE = 4;

    public function label(): string
    {
        return match ($this) {
            self::TRANSFER => 'โอนย้าย',
            self::REQUEST => 'ร้องขอทรัพยากร',
            self::STOP => 'ยุติคำขอ',
            self::RECEIVE => 'รับทรัพยากร',
        };
    }
}
