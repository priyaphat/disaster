<?php

namespace App\Console\Commands;

use App\Models\DisasterEarthquake;
use App\Models\DisasterWildfire;
use App\Models\Geography;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WildfireTask extends Command
{

    protected $signature = 'app:wildfire-task';

    public function handle()
    {
        $url = 'https://api-gateway.gistda.or.th/api/2.0/resources/features/viirs/1day';

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'API-Key' => '7iPj0ZjITZyxJTMh5LZYQhJLZXUBy73V1I1bnI2l9irjrVszLzaph3U6NuePSeXp',
        ])->get($url, [
            'offset' => 0,
            'ct_tn' => 'ราชอาณาจักรไทย',
            'pv_idn' => 57, // เชียงราย
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $features = collect($data['features'] ?? [])->pluck('properties');

            $grouped = $features->groupBy('amphoe')->map(function ($items, $amphoe) {
                return [
                    'district_id'   => Geography::where('district_name_th', $amphoe)->value('district_code'),
                    'district_name' => $amphoe,
                    'viirs'         => $items->count(),
                ];
            })->values();

            $createdAt = isset($features[0]['_createdAt'])
                ? Carbon::parse($features[0]['_createdAt'])
                : now();

            $data = new DisasterWildfire();
            $data->model = [
                'model' => $grouped,
                'line_oa' => 1
            ];
            $data->type = 2;
            $data->host = "api-gateway.gistda.or.th";
            $data->created_at =  $createdAt;
            $data->save();
        }
    }
}
