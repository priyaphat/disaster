<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\LineController;
use App\Http\Resources\ShelterDailyLogResource;
use App\Http\Resources\ShelterResource;
use App\Http\Utility\LinePostUtility;
use App\Models\DisasterEarthquake;
use App\Models\Shelter;
use App\Models\ShelterDailyLog;
use App\Models\ShelterPopulation;
use App\Services\PopulationService;
use App\Services\ReportService;
use App\Services\ShelterDailyLogService;
use App\Services\ShelterService;
use App\Services\ShelterServiceDailyLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SnapshotShelterTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:snapshot-shelter-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert shelter daily log and summary dashboard into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update or create shelter log
        $dateNow = Carbon::now()->format('Y-m-d');
        Shelter::select('id', 'name', 'status', 'max_capacity')
            ->chunk(100, function ($shelters) use ($dateNow) {
                foreach ($shelters as $shelter) {
                    $data = new ShelterDailyLogResource($shelter);
                    $data = array_merge(['date' => $dateNow], $data->toArray(request()));
                    app(ReportService::class)->updateShelter($data);
                }
            });

        // Update or create dashboard shelter log
        app(ReportService::class)->updateDashboard($dateNow);

        // Mock up database
        // $startDate = Carbon::createFromDate(now()->year, 1, 1);
        // $endDate = Carbon::createFromDate(now()->year, 10, 15);
        // $period = CarbonPeriod::create($startDate, $endDate);

        // foreach ($period as $now) {
        //     Shelter::query()
        //         ->chunk(100, function ($shelters) use ($now) {

        //             foreach ($shelters as $shelter) {

        //                 $maxCapacity = rand(10, 999);
        //                 $victims = $maxCapacity - rand(2, 5);
        //                 $a = rand(0, $victims);
        //                 $b = rand(0, $victims - $a);
        //                 // คำนวณตัวที่สามให้ครบ 9
        //                 $c = $victims - $a - $b;

        //                 ShelterDailyLog::updateOrCreate(
        //                     [
        //                         'log_date' => $now->format('Y-m-d'),
        //                         'shelter_id' => $shelter->id
        //                     ],
        //                     [
        //                         'shelter_id' => $shelter->id,
        //                         'status' => $shelter->status,
        //                         'max_capacity' => $maxCapacity,
        //                         'total_victims' => $victims,
        //                         'total_injuries' => $a,
        //                         'total_healthy' => $b,
        //                         'total_deaths' => $c
        //                     ]
        //                 );
        //             }
        //     });
        // }
    }
}
