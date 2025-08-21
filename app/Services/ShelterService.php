<?php

namespace App\Services;

use App\Enums\DamagedUtility;
use App\Enums\ShelterUtilities;
use App\Http\Controllers\API\LineController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ShelterProductController;
use App\Http\Resources\ShelterResource;
use App\Http\Utility\PaginateUtility;
use App\Models\DamagedUtility as ModelsDamagedUtility;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterPopulation;
use Illuminate\Support\Facades\DB;

class ShelterService
{
    public function getList(array $filters = [], bool $paginate = true, int $perPage = 10)
    {
        $query = Shelter::filter($filters)->with('fullAddress', 'populations');
        /** ShelterResource response time x 10 */
        if (!$paginate) {
            return ShelterResource::collection($query->get());
        } else {
            $data = $query->paginate($perPage);
            $paginate = $data->setCollection(ShelterResource::collection($data->items())->collection);
            return $paginate;
        }
    }

    public function create(array $data): Shelter
    {
        $shelter = Shelter::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'created_date' => $data['created_date'],
            'address' => $data['address'],
            'geography_id' => $data['geography_id'],
            'province_code' => $data['province_code'],
            'district_code' => $data['district_code'],
            'subdistrict_code' => $data['subdistrict_code'],
            'max_capacity' => $data['max_capacity'],
            'utilities' => $data['utilities'] ?? null, //uncomment this line when use in production
            'status' => $data['status'] ?? 1,
            'gis_map' => $data['gis_map'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            // 'utilities' => json_decode($data['utilities'], true), //decode use for dev only, comment or removed this line when use in production
        ]);

        app(ImageService::class)->uploadImage($data['image'] ?? null, Shelter::IMAGE_FOLDER, Shelter::class, $shelter->id);
        return $shelter;
    }

    public function update(Shelter $shelter, array $data, $updateImage = false): Shelter
    {
        // Create an instance of notification
        $notificationService = app(NotificationService::class);

        $shelter->update($data);
        $shelter->refresh();

        if ($shelter->wasChanged('status')) {
            if ($shelter->status === Shelter::CLOSED) {
                app(ShelterPopulationService::class)->deleteByShelter(['shelter_id' => $shelter->id]);
                app(AppointmentService::class)->deleteUpcomingAppointment(['shelter_id' => $shelter->id]);
                (new ShelterProductController)->transfer($shelter->id);
                (new ShelterProductController)->delete($shelter->id);
                // Send a closed shelter notification
                $notification = $notificationService->createNotification('shelter.closed', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
                $notificationService->sendToAllUsers($notification);
                (new LineController)->LineNotification($shelter->toArray(), LineController::Shelter_end);
            }

            if ($shelter->status === Shelter::OPEN) {
                // Send an opened shelter notification
                $notification = $notificationService->createNotification('shelter.opened', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
                $notificationService->sendToAllUsers($notification);
                (new LineController)->LineNotification($shelter->toArray(), LineController::Shelter_post);
            }
        }

        if ($updateImage) {
            $shelter->image = app(ImageService::class)->handleImageUpdate($data, Shelter::IMAGE_FOLDER, Shelter::class, $shelter->id);
        }

        return $shelter;
    }

    public function show(Shelter $shelter, array $search)
    {
        // $shelterLog = null;
        // // If request has date, find team log
        // if (isset($search['log_id']) && $search['log_id']) {
        //     $shelterLog = AppointmentLog::find($search['log_id']);
        // }

        // // If request has date, find team log
        // if ($shelterLog) {
        //     $data = collect($shelterLog->description);
        //     $data['image'] = $shelter->image?->image_path;
        //     return $data;
        // }

        $data = $shelter->load('image', 'population', 'leader', 'staff', 'geography');
        return new ShelterResource($data);
    }

    public function dashboard(array $filters = [])
    {
        $total_shelter = Shelter::where('status', Shelter::OPEN)->count();
        $total_population = Population::whereNotNull('shelter_id')->count();
        $updated_at = ShelterPopulation::max('updated_at');;

        $shelter_id = $filters['shelter_id'] ?? null;
        $query = Population::query();

        if ($shelter_id != null) {
            $query = $query->where('shelter_id', $shelter_id);
        }

        $total_injured = (clone $query)->where('status', Population::injure)->count();
        $total_healthy = (clone $query)->where('status', Population::healthy)->count();
        $total_dead = (clone $query)->where('status', Population::dead)->count();

        return $dashboard = [
            'total_shelter' => $total_shelter,
            'total_population' => $total_population,
            'updated_at' => $updated_at,
            'total_injured' => $total_injured,
            'total_healthy' => $total_healthy,
            'total_dead' => $total_dead,
        ];
    }

    public function getShelter($id)
    {
        return Shelter::findOrFail($id);
    }

    public function utility()
    {
        return array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->utility(),
        ], ShelterUtilities::cases());
    }

    public function utilities()
    {
        return array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], DamagedUtility::cases());
    }

    public function damaged_utilities($request, $type = null)
    {

        $data = ModelsDamagedUtility::query()->when(
            $type,
            fn($q) =>
            $q->where('type', $type)
        )->when(
            $request->name,
            fn($q) =>
            $q->where('name', 'like', '%' . $request->name . '%')
        );

        if ($type) return PaginateUtility::Paginate($data->get(), $request->paginate ?? 10);

        $data = $data->select('type', DB::raw('count(id) as total'))->groupBy('type')->get();
        return $data;
    }

    public function isShelterMax($id, $populationId = null): bool
    {
        $shelter = Shelter::find($id);
        $populationQuery = ShelterPopulation::where('shelter_id', $id);
        if ($populationId) {
            $populationQuery->where('population_id', '!=', $populationId);
        }
        $populationCount = $populationQuery->count();
        return $populationCount >= $shelter->max_capacity;
    }

    public function isShelterOpen($id): bool
    {
        $shelter = Shelter::find($id);
        return $shelter->status == Shelter::OPEN ? true : false;
    }
}
