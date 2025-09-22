<?php

namespace App\Enums;

enum CancellationReason: int
{
    // case USER_REQUEST = 1;
    // case DUPLICATE = 2;
    // case WRONG_INFO = 3;
    // case SYSTEM_ERROR = 4;
    case OTHER = 99;

    public function label(): string
    {
        return match ($this) {
            // self::USER_REQUEST => 'ผู้ใช้ร้องขอยกเลิก',
            // self::DUPLICATE => 'รายการซ้ำ',
            // self::WRONG_INFO => 'ข้อมูลไม่ถูกต้อง',
            // self::SYSTEM_ERROR => 'เกิดข้อผิดพลาดจากระบบ',
            self::OTHER => 'อื่น ๆ',
        };
    }
}
