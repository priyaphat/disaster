<?php

namespace App\Enums;

enum DisasterHelp: int
{
    case FLOOD = 1;
    case LANDSLIDE = 2;
    case WILDFIRE = 3;
    case SMOG = 4;
    case STORM = 5;
    case EARTHQUAKE = 6;
    case FIRE = 7;
    case EPIDEMIC = 8;
    case EVACUATE = 9;
    case SHELTER = 10;
    case HELP = 11;
    case SUPPORT = 12;
    case OTHER = 99;

    public function label(): string
    {
        return match ($this) {
            self::FLOOD => 'น้ำท่วม',
            self::LANDSLIDE => 'ดินถล่ม',
            self::WILDFIRE => 'ไฟป่า',
            self::SMOG => 'หมอกควัน',
            self::STORM => 'พายุฤดูร้อน',
            self::EARTHQUAKE => 'แผ่นดินไหว',
            self::FIRE => 'อัคคีภัย',
            self::EPIDEMIC => 'โรคระบาด',
            self::EVACUATE => 'อพยพ',
            self::SHELTER => 'เข้าศูนย์พักพิง',
            self::HELP => 'ขอทีมช่วยเหลือชีวิต',
            self::SUPPORT => 'ต้องการสิ่งสนับสนุนปัจจัยสี่',
            self::OTHER => 'อื่น ๆ',
        };
    }
}
