<?php

namespace App\Services;

use App\Models\Population;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PopulationService
{
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = Population::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['first_name', 'last_name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        $gender = $filters['gender'] ?? null;
        $status = $filters['status'] ?? null;

        if ($gender != null) {
            $query->where('gender', $gender);
        }

        if ($status != null) {
            $query->where('status', $status);
        }

        // Orders by 'created_at' DESC if ASC use oldest
        $query->latest();

        // limit the number of results
        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
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

    public function transfer(Population $population, array $data, $maxcap): Population
    {
        $populations = Population::where('shelter_id', $data['shelter_id'])->count();
        if ($populations >= $maxcap) {
            // throw new \Exception(__('error.shelter_full'));
            throw new \Exception(__('ศูนย์พักพิงเต็ม'));
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
}
