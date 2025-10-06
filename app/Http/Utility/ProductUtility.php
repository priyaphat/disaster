<?php

namespace App\Http\Utility;

use App\Models\Shelter;
use App\Models\ShelterStaff;

class ProductUtility
{

    public static function getShelter()
    {
        $shelter = ShelterStaff::where('user_id', Auth()->user()->id)->value('shelter_id');
        return $shelter ?? 0;
    }

    public static function shelterName($id)
    {
        return Shelter::find($id)->name ?? 'ศูนย์กลาง';
    }
}
