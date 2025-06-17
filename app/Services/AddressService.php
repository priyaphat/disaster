<?php

namespace App\Services;

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
}
