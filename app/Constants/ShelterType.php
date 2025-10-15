<?php

namespace App\Constants;

class ShelterType
{
    public static array $TYPES = [
            [
                "id" => 1,
                "name" =>  "ที่พักอาศัย"
            ],
            [
                "id" => 2,
                "name" =>  "หอพัก"
            ],
            [
                "id" => 3,
                "name" =>  "โรงพยาบาลส่งเสริมสุขภาพตําบล",
                "prefixes" => ["รพสต.", "รพ.สต.", "รพสต", "โรงพยาบาลส่งเสริมตำบล", "โรงพยาบาลส่งเสริมสุขภาพตําบล"]
            ],
            [
                "id" => 4,
                "name" =>  "โรงเรียน",
                "prefixes" => ["รร.", "ร.ร.", "ร.ร", "รร"]
            ],
            [
                "id" => 5,
                "name" =>  "โรงพยาบาล",
                "prefixes" => ["รพ.", "ร.พ.", "ร.พ", "รพ", "โรงบาล"]
            ],
            [
                "id" => 6,
                "name" =>  "โรงแรม",
                "prefixes" => ["รร.", "ร.ร.", "ร.ร", "รร"]
            ],
            [
                "id" => 7,
                "name" =>  "อื่นๆ"
            ]
    ];

    // Optional helper: find by id
    public static function findById(int $id): ?array
    {
        return collect(self::$TYPES)->firstWhere('id', $id);
    }
}
