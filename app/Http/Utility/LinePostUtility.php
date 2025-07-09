<?php

namespace App\Http\Utility;

use App\Http\Controllers\API\LineController;
use Illuminate\Support\Facades\Log;

class LinePostUtility
{
    public static function line_oa_call($form, $data)
    {
        $user = ['Uaa3e2b17e373580b16101b64937e4a39']; //test

        $data['line_oa'] = $data['line_oa'] ?? 0;
        if ($data['line_oa'] == 1) {

            if ($form == LineController::Disaster_post) {
                $data = [
                    'type' => $data['disaster_name'],
                    'level' => $data['level'],
                    'areas' => $data['areas']
                        ->filter(fn($item) => !empty($item['district_name']))
                        ->pluck('district_name'),
                ];
            }
            return (new LineController)->LineNotification($data, $form, $user);
        }
    }
}
