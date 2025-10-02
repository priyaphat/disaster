<?php

namespace App\Enums;

enum ProductLogs: int
{
    case TRANSFER = 1;
    case REQUEST = 2;
    case STOP = 3;
    case RECEIVE = 4;
    case UPDATE = 5;

    public function label(): string
    {
        return match ($this) {
            self::TRANSFER => 'โอนย้าย',
            self::REQUEST => 'ร้องขอทรัพยากร',
            self::STOP => 'ยุติคำร้อง',
            self::RECEIVE => 'ได้รับทรัพยากร',
            self::UPDATE => 'แก้ไขคำร้อง',
        };
    }
}
