<?php

namespace App\Enums;

enum WeatherCond: int
{
    case CLEAR = 1;
    case PARTLY_CLOUDY = 2;
    case CLOUDY = 3;
    case OVERCAST = 4;
    case LIGHT_RAIN = 5;
    case MODERATE_RAIN = 6;
    case HEAVY_RAIN = 7;
    case THUNDERSTORM = 8;
    case VERY_COLD = 9;
    case COLD = 10;
    case COOL = 11;
    case VERY_HOT = 12;

    public function label(): string
    {
        return match ($this) {
            self::CLEAR => 'Clear',
            self::PARTLY_CLOUDY => 'Partly Cloudy',
            self::CLOUDY => 'Cloudy',
            self::OVERCAST => 'Overcast',
            self::LIGHT_RAIN => 'Light Rain',
            self::MODERATE_RAIN => 'Moderate Rain',
            self::HEAVY_RAIN => 'Heavy Rain',
            self::THUNDERSTORM => 'Thunderstorm',
            self::VERY_COLD => 'Very Cold',
            self::COLD => 'Cold',
            self::COOL => 'Cool',
            self::VERY_HOT => 'Very Hot',
        };
    }

    public function labelTh(): string
    {
        return match ($this) {
            self::CLEAR => 'ท้องฟ้าแจ่มใส',
            self::PARTLY_CLOUDY => 'มีเมฆบางส่วน',
            self::CLOUDY => 'เมฆเป็นส่วนมาก',
            self::OVERCAST => 'มีเมฆมาก',
            self::LIGHT_RAIN => 'ฝนตกเล็กน้อย',
            self::MODERATE_RAIN => 'ฝนปานกลาง',
            self::HEAVY_RAIN => 'ฝนตกหนัก',
            self::THUNDERSTORM => 'ฝนฟ้าคะนอง',
            self::VERY_COLD => 'อากาศหนาวจัด',
            self::COLD => 'อากาศหนาว',
            self::COOL => 'อากาศเย็น',
            self::VERY_HOT => 'อากาศร้อนจัด',
        };
    }
};
