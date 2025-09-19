<?php

namespace App\Enums;

enum EmergencyLevel: int
{
    case S = 1;
    case M = 2;
    case L = 3;
    case XL = 4;

    public function label(): string
    {
        return match ($this) {
            self::S => 'ระดับ 1 สาธารณภัยขนาดเล็ก จัดการได้ภายในอำเภอ',
            self::M => 'ระดับ 2 จัดการได้ภายในจังหวัด',
            self::L => 'ระดับ 3 ระดับภาคหรือส่วนกลาง ระดับกรมเข้ามาช่วยจัดการ',
            self::XL => 'ระดับ 4 ระดับชาติ',
        };
    }
}
