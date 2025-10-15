<?php

namespace App\Console\Commands;

use App\Http\Utility\ShelterUtility;
use App\Services\ShelterPointService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddShelterPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-point-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch shelter points from external API and insert into DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://shingeometics.com/webgis/WebMapCRI/poi/poi_api.php';
        $response = Http::get($url);

        if ($response->successful()) {
            $json = $response->json();
            $features = $json['features'];
            collect($features)->chunk(100)->each(function ($items) {
                foreach ($items as $item) {
                    $data = $item['properties'];
                    $normalizedName = ShelterUtility::normalizePointName($data['name']);
                    app(ShelterPointService::class)->updateOrCreate([
                        'id' => $data['id'],
                        'name' => $data['name'],
                        'original_name' => $data['name'],
                        'normalized_name' => $normalizedName,
                        'map_url' => $data['map_url'],
                    ]);
                }
            });
            Log::info('Update External Point API');
        } else {
            Log::info('External Point API error: ' . $response->status());
        }
    }
}
