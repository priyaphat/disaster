<?php

namespace App\Enums;

enum FloodStatus: string
{
    case CRITICAL_LOW = '1';
    case LOW = '2';
    case NORMAL = '3';
    case HIGH = '4';
    case OVER_FLOW = '5';
    case OTHER = '6';

    public function label(): string
    {
        return match ($this) {
            self::CRITICAL_LOW => 'น้อยวิกฤต',
            self::LOW => 'น้อย',
            self::NORMAL => 'ปกติ',
            self::HIGH => 'มาก',
            self::OVER_FLOW => 'เกินความจุ',
            self::OTHER => 'ไม่ระบุ',
        };
    }
}
