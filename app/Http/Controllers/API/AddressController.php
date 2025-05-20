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
        $provinces = DB::table('thai_provinces')->get();
        return ApiResponse::successResponse(__('success.data'), $provinces);
    }

    public function getAmphures($provinceId)
    {
        $districts = DB::table('thai_amphures')->where('province_id', $provinceId)->get();
        return ApiResponse::successResponse(__('success.data'), $districts);
    }

    public function getTambons($amphureId)
    {
        $sub = DB::table('thai_tambons')->where('amphure_id', $amphureId)->get();
        return ApiResponse::successResponse(__('success.data'), $sub);
    }
}
