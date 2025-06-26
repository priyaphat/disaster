<?php

namespace App\Enums;

enum ActionType : int
{
    case ENTER = 1;
    case TRANSFER = 2;
    case EXIT = 3;
    case ASSIGN = 4 ;
    case JOIN_TEAM = 5;
    case EXIT_TEAM = 6;
    case OPEN = 7;
    case CLOSE = 8;

    public function action_type(): string
    {
        return match ($this) {
            self::ENTER => 'เข้าสู่ศูนย์พักพิง',
            self::TRANSFER => 'โอนย้ายศูนย์พักพิง',
            self::EXIT => 'ออกจากศูนย์พักพิง',
            self::ASSIGN => 'รับตำแหน่งหน้าที่',
            self::JOIN_TEAM => 'ย้ายเข้าทีมเจ้าหน้าที่',
            self::EXIT_TEAM => 'ออกจากทีมเจ้าหน้าที่',
            self::OPEN => 'เปิดทำการ',
            self::CLOSE => 'ปิดทำการ'
        };
    }
}
