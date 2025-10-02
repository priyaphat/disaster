<?php

namespace App\Services;

use App\Enums\DamagedUtility as Enums;
use App\Http\Utility\PaginateUtility;
use App\Models\DamagedUtility;
use Illuminate\Support\Facades\DB;

class DamageUtilityService
{
    public function utilities()
    {
        return array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], Enums::cases());
    }

    public function damaged_utility($id)
    {
        return DamagedUtility::select(
            'damaged_utilities.*',
            'geography.province_code',
            'geography.province_name_th',
            'geography.province_name_en',
            'geography.district_code',
            'geography.district_name_th',
            'geography.district_name_en',
            'geography.subdistrict_code',
            'geography.subdistrict_name_th',
            'geography.subdistrict_name_en',
            'geography.postal_code',
            DB::raw('concat("https://www.google.com/maps/search/?api=1&query=",damaged_utilities.name) as gis_map')
        )->leftJoin('geography', 'geography.id', 'damaged_utilities.geography_id')
            ->where('damaged_utilities.id', $id)->first();
    }

    public function damaged_utilities($request, $type = null)
    {
        $data = DamagedUtility::query()->when(
            $type,
            fn($q) =>
            $q->where('type', $type)
        )->when(
            isset($request['search']),
            fn($q) =>
            $q->where('name', 'like', '%' . $request['search'] . '%')
        );

        if ($type) return PaginateUtility::Paginate($data
            ->leftJoin('geography', 'geography.id', 'damaged_utilities.geography_id')
            ->select(
                'damaged_utilities.*',
                'geography.province_code',
                'geography.province_name_th',
                'geography.province_name_en',
                'geography.district_code',
                'geography.district_name_th',
                'geography.district_name_en',
                'geography.subdistrict_code',
                'geography.subdistrict_name_th',
                'geography.subdistrict_name_en',
                'geography.postal_code',
                DB::raw('concat("https://www.google.com/maps/search/?api=1&query=",damaged_utilities.name) as gis_map')
            )
            ->get(), $request['paginate'] ?? 10);

        $data = $data->select('type', DB::raw('count(id) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $utilities = collect($this->utilities())->map(function ($u) use ($data) {
            $u['total'] = $data[$u['id']] ?? 0;
            return $u;
        });

        return $utilities->values();
    }
}
