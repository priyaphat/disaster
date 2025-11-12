<?php

namespace App\Services;

use App\Enums\GenderId;
use App\Enums\PopulationStatus;
use App\Http\Resources\PopulationResource;
use App\Models\Population;
use App\Models\Prefix;
use App\Models\ShelterPopulation;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PopulationService
{
    #[QueryParameter('per_page', description: 'ส่งจำนวนข้อมูลที่ต้องการต่อหน้า', type: 'string', example: '3')]
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "มาม่า")]
    #[QueryParameter('unit_id', description: 'ค้นหาตามหน่วย ดึงหน่วยจาก api unit.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/unit.index', type: 'string', example: "1")]
    #[QueryParameter('category_id', description: 'ค้นหาตามประเภท ดึงประเภทจาก api category.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/category.index', type: 'string', example: "2")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    public function getList(array $filters = [], bool $paginate = false, int $perPage = 10)
    {
        $query = Population::filter($filters)->with('permanentAddress', 'currentAddress', 'prefix', 'shelter', 'image', 'shelterpopulation', 'group', 'allergy')->latest();
        if (!$paginate) {
            return PopulationResource::collection($query->get());
        } else {
            $data = $query->paginate($perPage);
            return $data->setCollection(PopulationResource::collection($data->items())->collection);
        }
    }

    public function summary(array $filters = [])
    {
        $query = Population::whereHas('shelter')->filter($filters);
        $total = $query->count();
        $group = $query->get()->countBy('status');
        $lastUpdated = ShelterPopulation::max('updated_at');
        return [
            'total_victims' => $total,
            'total_healthy' =>  $group[PopulationStatus::NORMAL->value] ?? 0,
            'total_injuries' => $group[PopulationStatus::INJURED->value] ?? 0,
            'total_deaths' => $group[PopulationStatus::DEATH->value] ?? 0,
            'updated_at' =>  $lastUpdated,
        ];
    }

    public function create(array $data): Population
    {
        if (isset($data['shelter_id']) && $data['shelter_id']) {
            $shelterService = app(ShelterService::class);

            if (!$shelterService->isShelterOpen($data['shelter_id'])) {
                throw new BadRequestHttpException(__('error.shelter.closed'));
            }
        }

        $population = Population::create($data);
        app(ShelterPopulationService::class)->updateOrCreate(['population_id' => $population->id, 'shelter_id' => $data['shelter_id'] ?? NULL]);
        app(ImageService::class)->uploadImage($data['image'] ?? null, Population::IMAGE_FOLDER, Population::class, $population->id);

        if (isset($data['allergy'])) {
            app(PopulationAllergyService::class)->updateOrCreate($population->id, $data['allergy']);
        }

        return $population;
    }

    public function update(Population $population, array $data, $isJson = true): Population
    {
        // Update population
        $population->update($data);
        // If request is form data, handle update image
        if (!$isJson) {
            app(ImageService::class)->handleImageUpdate($data, Population::IMAGE_FOLDER, Population::class, $population->id);
        }

        // Update shelter population by population
        if (isset($data['shelter_id']) && $data['shelter_id']) {
            app(ShelterPopulationService::class)->update($population, Arr::only($data, ['shelter_id']));
        }

        if (isset($data['allergy'])) {
            app(PopulationAllergyService::class)->updateOrCreate($population->id, $data['allergy']);
        }

        return $population;
    }

    public function show(Population $population)
    {
        $data = Population::with('permanentAddress', 'currentAddress', 'prefix', 'shelter', 'image', 'shelterpopulation', 'group', 'allergy')->findOrFail($population->id);
        return new PopulationResource($data);
    }

    public function search(array $filters = [])
    {
        $type = $filters['type'] ?? 1;
        $firstName = $filters['first_name'];
        $lastName = $filters['last_name'];
        $offset = $filters['offset'] ?? 0;
        $limit = $filters['limit'] ?? 100;

        $query = Population::select('id', 'first_name', 'last_name');
        if ($type == 1) {
            $data = $query->where('first_name', 'like', "%{$firstName}%")
                ->where('last_name', 'like', "%{$lastName}%")
                ->orderByRaw("CASE
            WHEN first_name LIKE ? THEN 1
            ELSE 2
            END, first_name ASC ", [$firstName . '%'])
                ->select('first_name')
                ->distinct()
                ->orderBy('first_name')
                ->skip($offset)
                ->take($limit)
                ->get()->pluck('first_name');
        } else {
            $data = $query->where('first_name', 'like', "%{$firstName}%")
                ->where('last_name', 'like', "%{$lastName}%")
                ->orderByRaw("CASE
            WHEN last_name LIKE ? THEN 1
            ELSE 2
            END, last_name ASC ", [$lastName . '%'])
                ->select('last_name')
                ->distinct()
                ->orderBy('last_name')
                ->skip($offset)
                ->take($limit)
                ->get()->pluck('last_name');
        }

        return $data;
    }

    public function checkPopulation(array $data)
    {
        $user = Population::where('first_name', $data['first_name'])
            ->where('last_name', $data['last_name'])
            ->where('prefix_id', $data['prefix_id'])
            ->with('permanentAddress', 'currentAddress', 'prefix', 'shelter', 'allergy')
            ->first();
        return $user ? new PopulationResource($user) : false;
    }

    public function addExternalPopulation()
    {
        $url = 'https://shingeometics.com/webgis/healthGIS/survey/sample_point.php';
        $response = Http::get($url);

        if ($response->successful()) {
            $json = $response->json();
            $features = $json['features'];
            collect($features)->chunk(10)->each(function ($items) {
                foreach ($items as $item) {
                    $data = $item['properties'];

                    $prefix = Prefix::where('name', $data['title'])->first();
                    if (!$prefix) {
                        $prefix = Prefix::create([
                            'name' => $data['title']
                        ]);
                        $prefixId = $prefix->id;
                    } else {
                        $prefixId = $prefix->id;
                    }

                    // $name = explode(' ', $data['name']);
                    $name = preg_split('/\s+/', trim($data['name']));
                    $firstName = $name[0] ?? '';
                    $lastName = $name[1] ?? '';

                    $birthDate = Carbon::parse($data['date_of_birth'])->format('Y-m-d');
                    $genderId = GenderId::label($prefixId)->value;
                    $sequenceId = $data['id'];
                    $phone = $data['tel'] ?? null;

                    $isOnDB = Population::where('sequence_id', $sequenceId)->first();

                    error_log('-----------------------------');
                    error_log('run population : ' . $data['id']);
                    if (!$isOnDB) {
                        // create population
                        error_log('create population : ' . $data['id']);
                        $request = [
                            'prefix_id' => $prefixId,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'birth_date' => $birthDate,
                            'gender' => $genderId,
                            'phone' => $phone,
                            'p_address' => null,
                            'p_geography_id' => null,
                            'c_address' => null,
                            'c_geography_id' => null,
                            'same_address' => 1,
                            'status' => PopulationStatus::NORMAL->value,
                            'sequence_id' => $sequenceId,
                            'created_date' => Carbon::now(),
                        ];

                        self::create($request);
                    }

                    error_log('-----------------------------');
                }
            });
        } else {
            Log::info('External API error: ' . $response->status());
        }
    }

    public function delete(Population $population): void
    {
        $population->image()?->delete();
        $population->allergy()?->delete();
        $population->assessments()?->delete();
        $population->shelterPopulation()?->delete();
        $population->delete();
        app(ImageService::class)->deleteImage(Population::class, $population->id);
    }
}
