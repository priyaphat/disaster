<?php

namespace App\Services;

use App\Enums\ShelterUtilities;
use App\Http\Resources\ShelterResource;
use App\Http\Resources\ShelterShowResource;
use App\Models\Population;
use App\Models\Shelter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ShelterService
{
    public function getList(array $filters = [])
    {
        $data = Shelter::filter($filters)->with('fulladdress', 'countpopulation')->paginate(10);
        return $data->setCollection(ShelterResource::collection($data->items())->collection);
    }

    public function create(array $data): Shelter
    {
        $shelter = Shelter::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'created_date' => $data['created_date'],
            'leader_id' => $data['leader_id'],
            'address' => $data['address'],
            'geography_id' => $data['geography_id'],
            'province_code' => $data['province_code'],
            'district_code' => $data['district_code'],
            'subdistrict_code' => $data['subdistrict_code'],
            'max_capacity' => $data['max_capacity'],
            'utilities' => $data['utilities'], //uncomment this line when use in production
            // 'utilities' => json_decode($data['utilities'], true), //decode use for dev only, comment or removed this line when use in production
        ]);

        return $shelter;
    }

    public function update(Shelter $shelter, array $data): Shelter
    {
        $shelter->update($data);
        return $shelter;
    }

    public function show(Shelter $shelter)
    {
        $data = Shelter::with('countpopulation', 'leader', 'countstaff', 'geography')->findOrFail($shelter->id);
        return new ShelterShowResource($data);
    }

    public function dashboard(array $filters = [])
    {
        $total_shelter = Shelter::where('status', Shelter::OPEN)->count();
        $total_population = Population::whereNotNull('shelter_id')->count();
        $updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $shelter_id = $filters['shelter_id'] ?? null;

        $query = Population::query();

        if ($shelter_id != null){
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
}
