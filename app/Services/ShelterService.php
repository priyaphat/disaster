<?php

namespace App\Services;

use App\Enums\DamagedUtility;
use App\Enums\PopulationStatus;
use App\Enums\ShelterUtilities;
use App\Exports\MultiSheetExport;
use App\Exports\ShelterExport;
use App\Http\Controllers\API\LineController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ShelterProductController;
use App\Http\Resources\ShelterResource;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\PaginateUtility;
use App\Http\Utility\ReportUtility;
use App\Http\Utility\ShelterUtility;
use App\Models\DamagedUtility as ModelsDamagedUtility;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterDailyLog;
use App\Models\ShelterPopulation;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Facades\Excel;

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
            'shelter_type' => $data['shelter_type'] ?? null,
            'gis_map' => $data['gis_map'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        app(ImageService::class)->uploadImage($data['image'] ?? null, Shelter::IMAGE_FOLDER, Shelter::class, $shelter->id);
        return $shelter;
    }

    public function update(Shelter $shelter, array $data): Shelter
    {
        $shelter->update($data);
        $shelter->refresh();

        // if ($shelter->wasChanged('status')) {
        //     if ($shelter->status === Shelter::CLOSED) {
        //         app(ShelterPopulationService::class)->deleteByShelter(['shelter_id' => $shelter->id]);
        //         app(AppointmentService::class)->deleteUpcomingAppointment(['shelter_id' => $shelter->id]);
        //         (new ShelterProductController)->transfer($shelter->id);
        //         (new ShelterProductController)->delete($shelter->id);
        //         // Send a closed shelter notification
        //         $notification = $notificationService->createNotification('shelter.closed', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
        //         $notificationService->sendToAllUsers($notification);
        //         (new LineController)->LineNotification($shelter->toArray(), LineController::Shelter_end);
        //     }

        //     if ($shelter->status === Shelter::OPEN) {
        //         // Send an opened shelter notification
        //         $notification = $notificationService->createNotification('shelter.opened', ['name' => $shelter->name], $shelter->id, Shelter::class, false);
        //         $notificationService->sendToAllUsers($notification);
        //         (new LineController)->LineNotification($shelter->toArray(), LineController::Shelter_post);
        //     }
        // }
        $shelter->image = app(ImageService::class)->handleImageUpdate($data, Shelter::IMAGE_FOLDER, Shelter::class, $shelter->id);
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

        $data = $shelter->load('image', 'populations', 'leader', 'staff', 'geography');
        return new ShelterResource($data);
    }

    public function dashboard(array $filters = [])
    {
        $totalShelter = Shelter::where('status', Shelter::OPEN)->count();
        $summary = app(PopulationService::class)->summary($filters);

        return $dashboard = [
            'total_shelter' => $totalShelter,
            'total_population' => $summary['total_victims'],
            'total_victims' =>  $summary['total_victims'],
            'total_injuries' => $summary['total_injuries'],
            'total_healthy' => $summary['total_healthy'],
            'total_deaths' => $summary['total_deaths'],
            'updated_at' => $summary['updated_at'],
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

    public function isShelterOpen($id): bool
    {
        $shelter = Shelter::find($id);
        return $shelter->status == Shelter::OPEN ? true : false;
    }

    public function getFileName($name = null): string
    {
        if (!$name) {
            return 'สรุปรายงานประจำศูนย์พักพิง';
        }
        return Str::of('รายงานจำนวนผู้ประสบภัยภายในศูนย์')->append($name);
    }

    public function export(?Shelter $shelter = null, array $filters)
    {
        $fileName = $this->getFileName($shelter?->name);
        $data = ReportUtility::getSnapshot($shelter, $filters);
        $rows = $shelter?->exists ? 8 : 7;
        $workSheet = [];
        if (count($data) > 0) {
            foreach ($data as $key => $sheet) {
                $sheetTitle = Carbon::createFromFormat('Y-m', $key)->thaidate('F Y');
                $workSheet[] = new ShelterExport($sheet, $sheetTitle, $rows);
            }
        } else {
            // Get default value
            $sheet = [ReportUtility::formatShelterData($shelter, [])];
            $workSheet[] = new ShelterExport($sheet, $sheetTitle = $fileName, $rows);
        }

        return Excel::download(new MultiSheetExport($workSheet), Str::of($fileName)->append('.xlsx'));
    }
}
