<?php

namespace App\Http\Utility;

use App\Constants\ShelterType;
use Illuminate\Support\Str;

class ShelterUtility
{
    public static function getGisMap(int $shelterType, string $name): string
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $name));
        $lowered = Str::lower($normalized);

        $type = ShelterType::findById($shelterType);

        $prefixes = $type['prefixes'] ?? [];
        $nameArr[] = $type['name'] ?? [];
        $prefixArr = array_merge($nameArr, $prefixes);

        foreach ($prefixArr as $prefix) {
            $lowerPrefix = Str::lower($prefix);
            if (Str::startsWith($lowered, $lowerPrefix)) {
                $normalized = trim(Str::substr($normalized, Str::length($prefix)));
                break;
            }
        }

        return trim($normalized);
    }
}
