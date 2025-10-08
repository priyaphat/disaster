<?php

namespace App\Services;

use App\Models\EmergencyRequest;
use App\Models\Population;
use App\Models\Shelter;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function getAddress(array $data)
    {
        $provinces = DB::table('geography')->where('id', $data['geography_id'])->get();
        return $provinces;
    }

    public function getProvinces()
    {
        // $data['province_name_th'] ??= "เชียงราย";
        $provinces = DB::table('geography')
            ->distinct()
            ->get(['province_name_th', 'province_code']);
        return $provinces;
    }

    public function getDistricts(array $data)
    {
        $districts = DB::table('geography')->where('province_code', $data['province_code'])
            ->select('district_name_th', 'district_code')
            ->distinct()
            ->get();
        return $districts;
    }

    public function getSubDistricts(array $data)
    {
        $sub_district = DB::table('geography')->where('district_code', $data['district_code'])
            ->select([
                'id as geography_id',
                'subdistrict_code',
                'subdistrict_name_th',
                'postal_code',
            ])
            ->distinct()
            ->get();
        return $sub_district;
    }

    /**
     * Search address from all tables that has address.
     */
    public function search(array $filters = [])
    {
        $search = $filters['search'] ?? null;
        $offset = $filters['offset'] ?? 0;
        $limit = $filters['limit'] ?? 10;

        // Search current address in populations
        $cAddress = Population::select('id', 'c_address as address')->where('c_address', 'like', "%{$search}%")
            ->orderByRaw("CASE
            WHEN c_address LIKE ? THEN 1
            ELSE 2
            END, c_address ASC ", [$search . '%'])->toBase();

        // Search permanent address in populations
       $pAddress = Population::select('id', 'p_address as address')->where('p_address', 'like', "%{$search}%")
            ->orderByRaw("CASE
            WHEN p_address LIKE ? THEN 1
            ELSE 2
            END, p_address ASC ", [$search . '%'])->toBase();

        // Search address in shelters
        $shelterAddress = Shelter::select('id', 'address')->where('address', 'like', "%{$search}%")
            ->orderByRaw("CASE
            WHEN address LIKE ? THEN 1
            ELSE 2
            END, address ASC ", [$search . '%'])->toBase();

        // Search address in emc requests
        $emcAdress = EmergencyRequest::select('id')->selectRaw("SUBSTRING_INDEX(address, ' ', CHAR_LENGTH(address) - CHAR_LENGTH(REPLACE(address, ' ', '')) - 3) AS address")
            ->whereRaw("SUBSTRING_INDEX(address, ' ', CHAR_LENGTH(address) - CHAR_LENGTH(REPLACE(address, ' ', '')) - 3) LIKE ?", ["%{$search}%"])->toBase();

        $unionQuery = $cAddress
            ->union($pAddress)
            ->union($shelterAddress)
            ->union($emcAdress);

        $data = DB::table(DB::raw("({$unionQuery->toSql()}) as unioned"))
            ->mergeBindings($unionQuery)
            ->distinct('address')
            ->orderBy('address')
            ->skip($offset)
            ->take($limit)
            ->pluck('address');

        return $data;
    }
}
