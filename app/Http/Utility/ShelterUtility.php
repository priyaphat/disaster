<?php

namespace App\Http\Utility;

use App\Constants\ShelterType;
use App\Models\ShelterPoint;
use Illuminate\Support\Str;

class ShelterUtility
{
    public static function normalizeShelterName(int $shelterType, string $name): string
    {
        // Trim space
        $normalized = trim(preg_replace('/\s+/', ' ', $name));

        $type = ShelterType::findById($shelterType);
        $prefixes = collect($type['prefixes'] ?? [])->push($type['name'] ?? []);

        // Normalized name by prefix
        foreach ($prefixes as $prefix) {
            if (Str::startsWith($normalized, $prefix)) {
                $normalized = trim(Str::substr($normalized, Str::length($prefix)));
                break;
            }
        }

        return trim($normalized);
    }

    public static function normalizePointName(string $name): string
    {
        // Trim space
        $normalized = trim(preg_replace('/\s+/', ' ', $name));

        // Map all prefixes with keu
        $types = collect(ShelterType::$TYPES)->whereIn('id', [3, 5])->values();
        $types = $types->mapWithKeys(function ($item) {
            return [$item['name'] => $item['prefixes']];
        });

        foreach ($types as $key => $prefixes) {
            // Search string for matched prefix in the given string
            $prefix = collect($prefixes)
                ->first(fn($p) => Str::startsWith($normalized, $p));
            // If it has prefix, normalized key and name
            if ($prefix) {
                $name = trim(Str::substr($normalized, Str::length($prefix)));
                $normalized = Str::of($key)->append(' ')->append($name);
                break;
            }
        }

        return trim($normalized);
    }

    public static function getGisMap(int $shelterType, string $name): string
    {
        $type = ShelterType::findById($shelterType);
        $normalized = Str::of($type['name'])->append(' ')->append($name);

        $point = ShelterPoint::where('original_name', $name)->first();
        if (!$point) {
            $point = ShelterPoint::where('normalized_name', $normalized)->first();
        }
        
        if ($point) {
            return $point->map_url;
        }

        return env('GIS_SHELTER_URL') . '?' . http_build_query(['name' => $name]);
    }
}
