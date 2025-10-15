<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\LineController;
use App\Http\Resources\ShelterResource;
use App\Http\Utility\LinePostUtility;
use App\Models\DisasterEarthquake;
use App\Models\Shelter;
use App\Models\ShelterDailyLog;
use App\Models\ShelterPopulation;
use App\Services\PopulationService;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $now = Carbon::now()->format('Y-m-d');
        $startDate = Carbon::createFromDate(now()->year, 2, 12);
        $endDate = now();

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $now) {
            Shelter::query()
                ->chunk(100, function ($shelters) use ($now) {

                    foreach ($shelters as $shelter) {

                        $maxCapacity = rand(10, 9999);
                        $currentCapacity = $maxCapacity - rand(2, 5);
                        $totalPopulation = $currentCapacity;
                        $a = rand(0, $currentCapacity);
                        $b = rand(0, $currentCapacity - $a);

                        // คำนวณตัวที่สามให้ครบ 9
                        $c = $currentCapacity - $a - $b;

                        // $totalPopulation = ShelterPopulation::where(['shelter_id' => $shelter->id])->count();
                        // $summary = app(PopulationService::class)->summary(['shelter_id' => $shelter->id]);

                        ShelterDailyLog::updateOrCreate(
                            [
                                'log_date' => $now,
                                'shelter_id' => $shelter['id']
                            ],
                            [
                                'shelter_id' => $shelter->id,
                                'status' => $shelter->status,
                                'max_capacity' => $maxCapacity,
                                'current_capacity' => $currentCapacity,
                                'total_populations' => $totalPopulation,
                                'total_injuries' => $a,
                                'total_healthy' => $b,
                                'total_deaths' => $c
                            ]
                        );
                        // ShelterDailyLog::updateOrCreate(
                        //     [
                        //         'log_date' => $now,
                        //         'shelter_id' => $shelter['id']
                        //     ],
                        //     [
                        //         'shelter_id' => $shelter->id,
                        //         'status' => $shelter->status,
                        //         'max_capacity' => $shelter->max_capacity,
                        //         'current_capacity' => $shelter->populations()->count(),
                        //         'total_populations' => $totalPopulation,
                        //         'total_injuries' => $summary['total_injuries'],
                        //         'total_healthy' => $summary['total_healthy'],
                        //         'total_deaths' => $summary['total_deaths']
                        //     ]
                        // );
                    }
                });
        }
    }
}
