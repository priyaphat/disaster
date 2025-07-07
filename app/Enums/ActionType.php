<?php

namespace App\Enums;

use App\Models\Population;

enum ActionType: int
{
    case ENTER = 1;
    case TRANSFER = 2;
    case EXIT = 3;
    case ASSIGN = 4;
    case JOIN_TEAM = 5;
    case EXIT_TEAM = 6;
    case CREATE_SHELTER = 7;
    case OPEN = 8;
    case CLOSE = 9;

    public function label(?string $model): string
    {
        if ($model === Population::class) {
            return match ($this) {
                self::ENTER => 'เข้าสู่ศูนย์',
                self::TRANSFER => 'ย้ายสู่ศูนย์',
                self::EXIT => 'นำออกจากศูนย์',
            };
        }

        return match ($this) {
            self::ENTER => 'เข้าสู่ศูนย์พักพิง',
            self::TRANSFER => 'โอนย้ายศูนย์พักพิง',
            self::EXIT => 'ออกจากศูนย์พักพิง',
            self::ASSIGN => 'รับตำแหน่งหน้าที่',
            self::JOIN_TEAM => 'ย้ายเข้าทีมเจ้าหน้าที่',
            self::EXIT_TEAM => 'ออกจากทีมเจ้าหน้าที่',
        };
    }
}
