<?php

namespace App\Enums;

enum AssessmentType: int
{
    //
    case ASSESSMENT = 1;
    case INSPECTION_VISIT = 2;
    case INSPECTION_CHILD = 3;

    public function label(): string
    {
        return match ($this) {
            self::ASSESSMENT => 'บันทึกการตรวจเยี่ยม',
            self::INSPECTION_VISIT => 'แบบบันทึกเวชระเบียนของทีมปฏิบัติการฉุกเฉิน',
            self::INSPECTION_CHILD => 'แบบบันทึกเวชระเบียนของทีมปฏิบัติการฉุกเฉิน (อายุน้อยกว่า 5 ปี)',
        };
    }
}
