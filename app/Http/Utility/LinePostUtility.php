<?php

namespace App\Http\Utility;

use App\Enums\DamagedUtility;
use App\Http\Controllers\API\LineController;
use Illuminate\Support\Facades\Log;

class LinePostUtility
{
    protected static $user = ['Uaa3e2b17e373580b16101b64937e4a39']; //test

    public static function line_oa_call($form, $data)
    {
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
            return (new LineController)->LineNotification($data, $form, self::$user);
        }
    }

    public static function LineMakeInformation($form, $data)
    {
        if ($form == LineController::Damaged_utility_post) {
            $data = [
                [
                    'utility' => DamagedUtility::tryFrom($data['data'][0]['type'])->label(),
                    'amount' => count($data['data']),
                    'name' => array_column($data['data'], 'name'),
                ]
            ];
        }
        if ($form == LineController::Damaged_utility_end) {
            $data = [
                [
                    'utility' => DamagedUtility::tryFrom($data)->label(),
                ],
            ];
        }
        return (new LineController)->LineNotification($data, $form, self::$user);
    }
}
