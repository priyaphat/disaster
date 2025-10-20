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
            if ($limit == 35000) {
                $name = 'โปร';
                $price = '1,780 บาท / เดือน';
                $rate = 0.06;
                $detail = [
                    'ข้อความส่งฟรี 35,000 ข้อความ / เดือน',
                    'ส่งข้อความเพิ่มเติมได้',
                    'ค่าบริการ ข้อความเพิ่มเติม 0.06 บาท/ข้อความ'
                ];
                if ($totalUsage > 35000) {
                    $sum = 1780 + (($totalUsage - 35000) * $rate);
                }
            } else if ($limit == 15000) {
                $name = 'เบสิค';
                $price = '1,280 บาท / เดือน';
                $detail = [
                    'ข้อความส่งฟรี 15,000 ข้อความ / เดือน',
                    'ส่งข้อความเพิ่มเติมได้',
                    'ค่าบริการ ข้อความเพิ่มเติม 0.1 บาท/ข้อความ'
                ];
                $rate = 0.1;
                if ($totalUsage > 15000) {
                    $sum = 1280 + (($totalUsage - 15000) * $rate);
                }
            } else {
                $name = 'ฟรี';
                $price = '0 บาท / เดือน';
                $detail = ['ข้อความส่งฟรี 300 ข้อความ / เดือน'];
                $rate = 0;
                $sum = 0;
            }

            Config::where('type', Config::lineCount)->update([
                "description" => [
                    "rate" => $rate,
                    "limited" => $limit,
                    "type" => 1,
                    "totalUsage" => $totalUsage,
                    "package" => $name,
                    "price" => $price,
                    "detail" => $detail,
                    "sum" => $sum,
                ]
            ]);
        }
    }
}
