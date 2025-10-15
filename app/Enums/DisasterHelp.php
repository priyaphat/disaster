<?php

namespace App\Enums;

enum DisasterHelp: int
{
    case FLOOD = 10;
    case LANDSLIDE = 20;
    case WILDFIRE = 30;
    case SMOG = 40;
    case STORM = 50;
    case EARTHQUAKE = 60;
    case FIRE = 70;
    case EPIDEMIC = 80;
    case EVACUATE = 90;
    case SHELTER = 100;
    case HELP = 110;
    case SUPPORT = 120;
    case OTHER = 999;

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
