<?php

namespace App\Console\Commands;

use App\Enums\WeatherCond;
use App\Http\Utility\DateUtility;
use App\Models\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherTask extends Command
{
    protected $signature = 'app:weather-task';
    protected $description = 'weather pull data daily';

    public function handle()
    {
        $w1day = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => env('WEATHER_BEARER'),
        ])->get('https://data.tmd.go.th/nwpapi/v1/forecast/location/hourly/place', [
            'hour' => 8,
            'fields' => 'tc,rh,cond,rain,ws10m',
            'duration' => 24,
            'province' => 'เชียงราย',
            'amphoe' => 'เมืองเชียงราย'
        ]);

        $w7day = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => env('WEATHER_BEARER'),
        ])->get('https://data.tmd.go.th/nwpapi/v1/forecast/location/daily/place', [
            'fields' => 'tc_max,cond,rain,tc',
            'duration' => 7,
            'province' => 'เชียงราย',
            'amphoe' => 'เมืองเชียงราย'
        ]);

        if ($w1day->successful()) {
            $weather['location'] = $w1day['WeatherForecasts'][0]['location'];
            $weather['hour'] =  collect($w1day['WeatherForecasts'][0]['forecasts'])->map(function ($forecast) {
                $forecast['data']['cond_text'] = WeatherCond::tryFrom($forecast['data']['cond'])->labelTh();
                $forecast['data']['day'] = DateUtility::days($forecast['time']);
                $forecast['data']['month'] = DateUtility::months($forecast['time']);
                return $forecast;
            })->toArray();

            if ($w7day->successful()) {
                $weather['7day'] = collect($w7day['WeatherForecasts'][0]['forecasts'])->map(function ($forecast) {
                    $forecast['data']['cond_text'] = WeatherCond::tryFrom($forecast['data']['cond'])->labelTh();
                    $forecast['data']['day'] = DateUtility::days($forecast['time']);
                    $forecast['data']['month'] = DateUtility::months($forecast['time']);
                    return $forecast;
                })->toArray();
            }
            // Log::info($weather);
        } else {
            Log::error('Weather error pulling data. Status: ' . $w1day->status() . ' at ' . now());
        }

        $data['name'] = 'weather';
        $data['type'] = Config::weather;
        $data['description'] = $weather;
        $config = Config::where('type', Config::weather)->first();
        if ($config) {
            $config->update($data);
            Log::info('Weather pulled data at ' . now());
        } else {
            $data['status'] = 
            $data = Config::create($data);
            Log::info('Weather pulled data at ' . now());
        }
    }
}
