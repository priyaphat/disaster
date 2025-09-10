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
        // //

        // $url = 'https://shingeometics.com/webgis/healthGIS/survey/sample_point.php';
        // $response = Http::get($url);

        // if ($response->successful()) {
        //     $json = $response->json();
        //     $features = $json['features'];
        //     collect($features)->chunk(10)->each(function ($items) {
        //         foreach ($items as $item) {
        //             $data = $item['properties'];

        //             $prefix = Prefix::where('name', $data['title'])->first();
        //             if (!$prefix) {
        //                 $prefix = Prefix::create([
        //                     'name' => $data['title']
        //                 ]);
        //                 $prefixId = $prefix->id;
        //             } else {
        //                 $prefixId = $prefix->id;
        //             }

        //             // $name = explode(' ', $data['name']);
        //             $name = preg_split('/\s+/', trim($data['name']));
        //             $firstName = $name[0] ?? '';
        //             $lastName = $name[1] ?? '';

        //             $birthDate = Carbon::parse($data['date_of_birth'])->format('Y-m-d');
        //             $genderId = GenderId::label($prefixId)->value;
        //             $sequenceId = $data['id'];
        //             $phone = $data['tel'] ?? null;

        //             $isOnDB = Population::where('sequence_id', $sequenceId)->first();

        //             error_log('-----------------------------');
        //             error_log('run population : ' . $data['id']);
        //             if (!$isOnDB) {
        //                 // create population
        //                 error_log('create population : ' . $data['id']);
        //                 $request = [
        //                     'prefix_id' => $prefixId,
        //                     'first_name' => $firstName,
        //                     'last_name' => $lastName,
        //                     'birth_date' => $birthDate,
        //                     'gender' => $genderId,
        //                     'phone' => $phone,
        //                     'p_address' => null,
        //                     'p_geography_id' => null,
        //                     'c_address' => null,
        //                     'c_geography_id' => null,
        //                     'same_address' => 0,
        //                     'status' => 1,
        //                     'sequence_id' => $sequenceId,
        //                     'created_date' => Carbon::now(),
        //                 ];

        //                 app(PopulationService::class)->create($request);
        //             }

        //             error_log('-----------------------------');
        //         }
        //     });
        // } else {
        //     Log::info('External API error: '. $response->status());
        // }
    }
}
