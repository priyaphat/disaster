<?php

namespace App\Services;

use App\Http\Resources\PopulationResource;
use App\Http\Resources\PopulationShowResource;
use App\Models\Population;
use App\Models\Shelter;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PopulationService
{
    public function getList(array $filters = [])
    {
        $query = Population::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['first_name', 'last_name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        $gender = $filters['gender'] ?? null;
        $status = $filters['status'] ?? null;
        $sort = $filters['sort'] ?? 'desc';

        if ($gender != null) {
            $query->where('gender', $gender);
        }
        if ($status != null) {
            $query->where('status', $status);
        }
        if ($sort != null) {
            $query->orderBy('created_at', $sort);
        }

        $data = $query->with('fullpaddress', 'fullcaddress', 'prefix')->paginate(10);
        return $data->setCollection(PopulationResource::collection($data->items())->collection);
    }

    public function create(array $data): Population
    {
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
            // 'tracking' => $data['tracking'],
            'shelter_id' => $data['shelter_id'],
            'line_token' => $data['line_token'],
            'created_date' => $data['created_date'],
        ]);

        return $population;
    }

    public function update(Population $population, array $data): Population
    {
        $population->update($data);
        return $population;
    }

    public function show(Population $population)
    {
        $data = Population::with('fullpaddress', 'fullcaddress', 'prefix', 'shelter')->findOrFail($population->id);
        return new PopulationShowResource($data);
    }

    public function transfer(Population $population, array $data, $maxcap): Population
    {
        $populations = Population::where('shelter_id', $data['shelter_id'])->count();
        if ($populations >= $maxcap) {
            // throw new \Exception(__('error.shelter_full'));
        } else {
            $population->update([
                'shelter_id' => $data['shelter_id'],
            ]);
        }

        return $population;
    }

    public function isShelterClear($id): bool
    {
        return Population::where('shelter_id', $id)->count() === 0;
    }

    public function isShelterMax($id): bool
    {
        $shelter = Shelter::find($id);
        $populationCount = Population::where('shelter_id', $id)->count();
        return $populationCount >= $shelter->max_capacity;
    }
}
