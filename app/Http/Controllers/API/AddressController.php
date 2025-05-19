<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\District;
use App\Models\Province;
use App\Models\SubDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    //
    public function getProvinces()
    {
        $provinces = DB::table('provinces')->get();
        return ApiResponse::successResponse(__('success.data'), $provinces);
    }

    public function getDistricts($provinceId)
    {
        $districts = DB::table('districts')->where('province_id', $provinceId)->get();
        return ApiResponse::successResponse(__('success.data'), $districts);
    }

    public function getSubDistricts($districtId)
    {
        $sub = DB::table('sub_districts')->where('district_id', $districtId)->get();
        return ApiResponse::successResponse(__('success.data'), $sub);
    }
}
