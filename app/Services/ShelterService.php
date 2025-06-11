<?php

namespace App\Services;

use App\Enums\ShelterUtilities;
use App\Http\Resources\ShelterResource;
use App\Http\Resources\ShelterShowResource;
use App\Models\Shelter;
use Illuminate\Database\Eloquent\Collection;

class ShelterService
{
    public function getList(array $filters = [])
    {
        $query = Shelter::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['first_name', 'last_name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        $status = $filters['status'] ?? null;
        $district = $filters['district_code'] ?? null;
        $sort = $filters['sort'] ?? 'desc';

        if ($status != null) {
            $query->where('status', $status);
        }
        if ($district != null) {
            $query->where('district_code', $district);
        }
        if ($sort != null) {
            $query->orderBy('created_at', $sort);
        }
        $data = $query->with('fulladdress', 'countpopulation')->paginate(10);

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
            // 'utilities' => $data['utilities'], //uncomment this line when use in production
            'utilities' => json_decode($data['utilities'], true), //decode use for dev only, comment or removed this line when use in production
        ]);

        return $shelter;
    }

    public function update(Shelter $shelter, array $data): Shelter
    {
        $shelter->update($data);
        if ($shelter->wasChanged('status')) {
            //Collcet log shelter status change
        }
        return $shelter;
    }

    public function show(Shelter $shelter)
    {
        $data = Shelter::with('countpopulation', 'leader', 'countstaff', 'geography')->findOrFail($shelter->id);
        return new ShelterShowResource($data);
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
