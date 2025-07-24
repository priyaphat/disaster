<?php

namespace App\Http\Utility;

use App\Models\ShelterStaff;

class ProductUtility
{

    public static function getShelter()
    {
        $shelter = ShelterStaff::where('user_id', Auth()->user()->id)->value('shelter_id');
        return $shelter ?? 0;
    }
}
