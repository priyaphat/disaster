<?php

namespace App\Enums;

enum ActionType : int
{
    case EXIT = 1;
    case TRANSFER = 2;
    case ENTER = 3;
    case ASSIGN = 4 ;
    case JOIN_TEAM = 5;
    case CHANGE_TEAM = 6;

    public function action_type(): string
    {
        return match ($this) {
            self::EXIT => 'นำออกจากศูนย์',
            self::TRANSFER => 'ย้ายไปยังศูนย์',
            self::ENTER => 'เข้าสู่ศูนย์',
            self::ASSIGN => 'รับหน้าที่',
            self::JOIN_TEAM => 'ย้ายออกจากทีม',
            self::CHANGE_TEAM => 'ย้ายเข้าทีม'
        };
    }
}
