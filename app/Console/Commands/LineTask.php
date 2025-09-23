<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\DisasterEarthquake;
use App\Models\DisasterWildfire;
use App\Models\Geography;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LineTask extends Command
{

    protected $signature = 'app:line-task';

    public function handle()
    {
        $totalUsageUrl = 'https://api.line.me/v2/bot/message/quota/consumption';
        $limitUrl = 'https://api.line.me/v2/bot/message/quota';

        $totalUsage = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('LINE_BOT_CHANNEL_ACCESS_TOKEN'),
        ])->get($totalUsageUrl);

        $limit = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('LINE_BOT_CHANNEL_ACCESS_TOKEN'),
        ])->get($limitUrl);

        if ($totalUsage->successful() && $limit->successful()) {
            $totalUsage = $totalUsage->json();
            $limit = $limit->json();

            $totalUsage = $totalUsage['totalUsage'];
            $limit = $limit['value'];
            if ($limit == 15000) $rate = 0.1;
            else if ($limit == 35000) $rate = 0.06;
            else $rate = 0;

            Config::where('type', Config::lineCount)->update([
                "description" => [
                    "rate" => $rate,
                    "limited" => $limit,
                    "type" => 1,
                    "totalUsage" => $totalUsage,
                ]
            ]);
        }
    }
}
