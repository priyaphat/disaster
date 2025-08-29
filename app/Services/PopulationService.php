<?php

namespace App\Services;

use App\Enums\GenderId;
use App\Enums\PopulationStatus;
use App\Enums\PrefixId;
use App\Http\Resources\PopulationResource;
use App\Http\Resources\PopulationShowResource;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterPopulation;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PopulationService
{
    #[QueryParameter('per_page', description: 'ส่งจำนวนข้อมูลที่ต้องการต่อหน้า', type: 'string', example: '3')]
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "มาม่า")]
    #[QueryParameter('unit_id', description: 'ค้นหาตามหน่วย ดึงหน่วยจาก api unit.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/unit.index', type: 'string', example: "1")]
    #[QueryParameter('category_id', description: 'ค้นหาตามประเภท ดึงประเภทจาก api category.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/category.index', type: 'string', example: "2")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    public function getList(array $filters = [])
    {
        $query = Population::filter($filters);
        $data = $query->with('permanentAddress', 'currentAddress', 'prefix', 'shelter', 'image', 'shelterpopulation')->latest()->paginate(10);
        return $data->setCollection(PopulationResource::collection($data->items())->collection);
    }

    public function summary(array $filters = [])
    {
        $query = Population::whereHas('shelter')->filter($filters);
        $total = $query->count();
        $group = $query->get()->countBy('status');
        $lastUpdated = ShelterPopulation::max('updated_at');
        return [
            'total_victims' => $total,
            'total_injuries' => $group[PopulationStatus::INJURED->value] ?? 0,
            'total_deaths' => $group[PopulationStatus::DECREASE->value] ?? 0,
            'last_updated' =>  $lastUpdated,
        ];
    }

    public function create(array $data): Population
    {
        if (isset($data['shelter_id']) && $data['shelter_id']) {
            $shelterService = app(ShelterService::class);

            if ($shelterService->isShelterMax($data['shelter_id'])) {
                throw new BadRequestHttpException(__('error.shelter.full'));
            }

            if (!$shelterService->isShelterOpen($data['shelter_id'])) {
                throw new BadRequestHttpException(__('error.shelter.closed'));
            }
        }

        $population = Population::create([
            'prefix_id' => $data['prefix_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'p_address' => $data['p_address'],
            'p_geography_id' => $data['p_geography_id'],
            'c_address' => $data['c_address'],
            'c_geography_id' => $data['c_geography_id'],
            'same_address' => $data['same_address'],
            'status' => $data['status'],
            'created_date' => $data['created_date'],
        ]);

        app(ShelterPopulationService::class)->updateOrCreate(['population_id' => $population->id, 'shelter_id' => $data['shelter_id'] ?? NULL]);

        return $population;
    }

    public function update(Population $population, array $data): Population
    {
        if (isset($data['shelter_id'])) {
            app(ShelterPopulationService::class)->update($population, Arr::only($data, ['shelter_id']));
        }
        $population->update($data);
        return $population;
    }

    public function show(Population $population)
    {
        $data = Population::with('permanentAddress', 'currentAddress', 'prefix', 'shelter', 'image')->findOrFail($population->id);
        return new PopulationShowResource($data);
    }

    public function checkPopulation(array $data)
    {
        $user = Population::where('first_name', $data['first_name'])
            ->where('last_name', $data['last_name'])
            ->where('prefix_id', $data['prefix_id'])
            ->with('permanentAddress', 'currentAddress', 'prefix', 'shelter')
            ->first();
        return $user ? new PopulationShowResource($user) : false;
    }

    public function addExternalPopulation()
    {
        //Uncomment if timeout
        // ini_set('max_execution_time', 600);
        $url = 'https://shingeometics.com/webgis/healthGIS/survey/sample_point.php';
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $json = $response->json();
                $datas = $json['features'];

                foreach ($datas as $data) {
                    $data = $data['properties'];

                    $prefix_id = PrefixId::from($data['title'])->label();
                    // $name = explode(' ', $data['name']);
                    $name = preg_split('/\s+/', trim($data['name']));
                    $first_name = $name[0] ?? '';
                    $last_name = $name[1] ?? '';

                    $birth_date = Carbon::parse($data['date_of_birth'])->format('Y-m-d');
                    $gender_id = GenderId::label($prefix_id)->value;
                    $sequence_id = $data['id'];
                    $phone = $data['tel'] ?? '';

                    $isOnDB = Population::where('prefix_id', $prefix_id)
                        ->where('first_name', $first_name)
                        ->where('last_name', $last_name)
                        ->whereDate('birth_date', $birth_date)
                        ->first();

                    if (!$isOnDB) {
                        Population::create([
                            'prefix_id' => $prefix_id,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'birth_date' => $birth_date,
                            'gender' => $gender_id,
                            'phone' => $phone,
                            'p_address' => null,
                            'p_geography_id' => null,
                            'c_address' => null,
                            'c_geography_id' => null,
                            'sequence_id' => $sequence_id,
                            'created_date' => Carbon::now(),
                        ]);
                    }
                }

                return $datas;
            } else {
                throw new \Exception('External API error: ' . $response->status());
            }
        } catch (\Throwable $e) {
            throw new \Exception('Cannot fetch external population: ' . $e->getMessage());
        }
    }
}
