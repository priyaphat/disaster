<?php

namespace App\Console\Commands;

use App\Enums\GenderId;
use App\Enums\PrefixId;
use App\Models\Population;
use App\Models\Prefix;
use App\Services\ImageService;
use App\Services\PopulationService;
use App\Services\ShelterPopulationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddPopulationTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-population-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch populations from external API and insert into DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(PopulationService::class)->addExternalPopulation();
    }
}
