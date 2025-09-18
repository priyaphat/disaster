<?php

namespace App\Console\Commands;

use App\Models\DisasterEarthquake;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WildfireTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wildfire-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'API-Key' => '7iPj0ZjITZyxJTMh5LZYQhJLZXUBy73V1I1bnI2l9irjrVszLzaph3U6NuePSeXp',
        ])->get('https://api-gateway.gistda.or.th/api/2.0/resources/features/viirs/1day', [
            'offset' => 0,
            'ct_tn' => 'ราชอาณาจักรไทย',
            'pv_idn' => 57, // เชียงราย
        ]);

        $data = $response->json();
        $data = $response->json()['DailyEarthquakes'];
        $search = 'เชียงราย';
        $areas = collect($data)->filter(function ($item) use ($search) {
            return Str::contains($item['OriginThai'], $search);
        })->values();

        $start = Carbon::now()->subDay()->setTime(12, 00, 00)->format('Y-m-d H:i:s');
        $end = Carbon::now()->setTime(11, 59, 59)->format('Y-m-d H:i:s');
        // $start = Carbon::now()->setDate(2025, 01, 01)->setTime(12, 00, 00)->format('Y-m-d H:i:s');
        // $end = Carbon::now()->setDate(2025, 02, 28)->setTime(12, 00, 00)->format('Y-m-d H:i:s');
        $areas = $areas->whereBetween('DateTimeThai', [$start, $end])->values();

        foreach ($areas as $area) {
            DisasterEarthquake::create([
                'address' => $area['TitleThai'],
                'latitude' => $area['Latitude'],
                'longitude' => $area['Longitude'],
                'depth' => $area['Longitude'],
                'magnitude' => $area['Magnitude'],
                'occurrence_time' => $area['DateTimeThai']
            ]);
        }
    }
}
