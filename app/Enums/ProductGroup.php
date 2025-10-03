<?php

namespace App\Enums;

enum ProductGroup: int
{
    case Consumer = 1;
    case Engine = 2;
    case Medical = 3;
    case Medicine = 4;

    public function label(): string
    {
        return match ($this) {
            self::Consumer => 'อุปโภคบริโภค',
            self::Engine => 'เครื่องจักร เครื่องมือต่างๆ',
            self::Medical => 'อุปกรณ์ทางการแพทย์',
            self::Medicine => 'ยารักษาโรค',
        };
    }
}