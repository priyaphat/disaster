<?php

namespace App\Enums;

enum PrefixId: string
{
    //
    case PREFIX_1 = 'นาย';
    case PREFIX_2 = 'นาง';
    case PREFIX_3 = 'นางสาว';
    case PREFIX_4 = 'เด็กชาย';
    case PREFIX_5 = 'เด็กหญิง';
    case PREFIX_6 = 'ดร.';
    case PREFIX_7 = 'ศ.';
    case PREFIX_8 = 'อื่น ๆ';

    public function label(): int
    {
        return match ($this) {
            self::PREFIX_1 => 1,
            self::PREFIX_2 => 2,
            self::PREFIX_3 => 3,
            self::PREFIX_4 => 4,
            self::PREFIX_5 => 5,
            self::PREFIX_6 => 6,
            self::PREFIX_7 => 7,
            self::PREFIX_8 => 8,
        };
    }
}
