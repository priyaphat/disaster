<?php

namespace App\Console\Commands;

use App\Models\Population;
use App\Models\PopulationDailyLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class SnapshotPopulationTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:snapshot-population-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert population daily log into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d');
        Population::query()
            ->chunk(100, function ($populations) use ($now) {

                foreach ($populations as $population) {

                    PopulationDailyLog::updateOrCreate(
                        [
                            'log_date' => $now,
                            'population_id' => $population->id
                        ],
                        [
                            'shelter_id' => $population->shelterPopulation?->shelter_id ?? null,
                            'status' => $population->status,
                            'death_cause' => $population->death_cause,
                        ]
                    );
                }
            });


        // Mock up database
        // $startDate = Carbon::createFromDate(now()->year, 1, 1);
        // $endDate = now();

        // $period = CarbonPeriod::create($startDate, $endDate);

        // foreach ($period as $now) {

        //     Population::query()
        //         ->chunk(100, function ($populations) use ($now) {

        //             foreach ($populations as $population) {

        //                 PopulationDailyLog::updateOrCreate(
        //                     [
        //                         'log_date' => $now,
        //                         'population_id' => $population->id
        //                     ],
        //                     [
        //                         'shelter_id' => $population->shelterPopulation?->shelter_id ?? null,
        //                         'status' => $population->status,
        //                         'death_cause' => $population->death_cause,
        //                     ]
        //                 );
        //             }
        //         });
        // }
    }
}
