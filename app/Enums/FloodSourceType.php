<?php

namespace App\Enums;

enum FloodSourceType: string
{
    case CANAL = '1';
    case RIVER = '2';
    case RESERVOIR = '3';
    case POND = '4';
    case IRRIGATION_CANNEL = '5';
    case MARSH = '6';

    public function label(): string
    {
        return match ($this) {
            self::CANAL => 'คลอง',
            self::RIVER => 'แม่น้ำ',
            self::RESERVOIR => 'อ่างเก็บน้ำ',
            self::POND => 'บึง/หนองน้ำ',
            self::IRRIGATION_CANNEL => 'คลองชลประทาน',
            self::MARSH => 'หนอง/ทุ่งน้ำ',
        };
    }
}
