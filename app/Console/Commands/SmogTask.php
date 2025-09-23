<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\LineController;
use App\Http\Utility\LinePostUtility;
use App\Models\DisasterSmog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SmogTask extends Command
{

    protected $signature = 'app:smog-task';
    protected $description = 'Command description';

    public function handle()
    {
        //
        $url = 'http://air4thai.pcd.go.th/services/getNewAQI_JSON.php?region=2';
        $response = Http::get($url);
        $data = $response->json()['stations'];
        $search = 'เชียงราย';
        $areas = collect($data)->filter(function ($item) use ($search) {
            return Str::contains($item['areaTH'], $search);
        })->values();

        $i = [];
        foreach ($areas as $area) {
            // if (preg_match('/ต\.(.*?)\s+อ\.(.*?),\s*(.*)/u', $area['areaTH'], $matches)) {
            //     $subDistrict   = trim($matches[1]);   // เวียง
            //     $district   = trim($matches[2]);   // เมือง
            //     $province = trim($matches[3]);   // เชียงราย
            //     $geo = Geography::where('province_name_th', 'LIKE', "%{$province}%")
            //     ->where('district_name_th', 'LIKE', "%{$district}%")
            //     ->where('subdistrict_name_th', 'LIKE', "%{$subDistrict}%")
            //     ->first();
            // }
            $address = str_replace(", ", " จ.", $area['areaTH']);
            $aqi = $area['AQILast']['PM25']['value'] ?? '-';
            $status = self::getStatus($aqi);
            $data = [
                'source' => $area['nameTH'],
                'address' => $address,
                'status' => $status,
                'smog_value' => $aqi,
                'host' => 'air4thai.pcd.go.th',
            ];
            DisasterSmog::create($data);
            LinePostUtility::line_oa_call(LineController::Disaster_smog, $data);
        }

        // Log::info("Get smog data from external API");
    }

    private function getStatus($aqi)
    {
        if ($aqi <= 25) {
            return 1;
        } else if ($aqi <= 50) {
            return 2;
        } else if ($aqi <= 100) {
            return 3;
        } else if ($aqi <= 200) {
            return 4;
        } else {
            return 5;
        }
    }
}
