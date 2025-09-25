<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\LineController;
use App\Http\Utility\LinePostUtility;
use App\Models\DisasterFlood;
use App\Models\Geography;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FloodTask extends Command
{

    protected $signature = 'app:flood-task';

    public function handle()
    {
        $url = 'https://api-gateway.gistda.or.th/api/2.0/resources/features/flood/1days?offset=0&pv_idn=57'; //&pv_idn=57

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'API-Key' => '7iPj0ZjITZyxJTMh5LZYQhJLZXUBy73V1I1bnI2l9irjrVszLzaph3U6NuePSeXp',
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();
            $features = collect($data['features'] ?? [])->pluck('properties');

            if (!empty($features->toArray())) {
                foreach ($features as $i) {
                    $q = [
                        'address' => $i['pv_tn'] . ' ' . $i['ap_tn'] . ' ' . $i['tb_tn'],
                        'water_source' => $i['tb_tn'],
                        'water_source_type' => 7,
                        'status' => 6,
                        'water_value' => 0,
                        'geography_id' => Geography::where('subdistrict_name_th', preg_replace('/^ต\./u', '', $i['tb_tn']))->value('id'),
                        'line_oa' => 1,
                        'created_at' => $i['_createdAt'],
                        'host' => 'api-gateway.gistda.or.th',
                    ];
                    DisasterFlood::create($q);
                    LinePostUtility::line_oa_call(LineController::Disaster_flood, $q);
                }
            }
        }
    }
}
