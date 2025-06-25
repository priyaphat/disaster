<?php

namespace App\Enums;

enum DamagedUtility: string
{
    case GOVERNMENT = '1';
    case SCHOOL = '2';
    case ELECTRICAL = '3';
    case WATER_TREATMENT = '4';

    public function label(): string
    {
        return match ($this) {
            self::GOVERNMENT => 'หน่วยงานราชการ',
            self::SCHOOL => 'โรงเรียน',
            self::ELECTRICAL => 'โรงจำหน่ายไฟฟ้า',
            self::WATER_TREATMENT => 'โรงบำบัดน้ำประปา',
        };
    }
}
