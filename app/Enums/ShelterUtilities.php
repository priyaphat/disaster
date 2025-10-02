<?php

namespace App\Enums;

enum ShelterUtilities: int
{
    case WATER = 1;
    case Electricity = 2;
    case Water_Treatment = 3;
    case Sanitary = 4;
    case Disposal = 5;
    case Internet = 6;
    case OTHERS = 7;

    public function utility(): string
    {
        return match ($this) {
            self::WATER => 'น้ำประปา',
            self::Electricity => 'ไฟฟ้า',
            self::Water_Treatment => 'บำบัดน้ำเสีย',
            self::Sanitary => 'สุขาภิบาล (ห้องน้ำ)',
            self::Disposal => 'การกำจัดขยะ',
            self::Internet => 'สัญญาณอินเทอร์เน็ต',
            self::OTHERS => 'อื่นๆ'
        };
    }
}
