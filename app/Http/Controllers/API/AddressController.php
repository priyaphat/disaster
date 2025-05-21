<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Geography\AddressRequest;
use App\Http\Requests\Geography\DistrictRequest;
use App\Http\Requests\Geography\SubDistrictRequest;
use App\Http\Responses\ApiResponse;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function getAddress(AddressRequest $request)
    {
        $address = $this->addressService->getAddress($request->all());
        return ApiResponse::successResponse(__('success.data'), $address);
    }

    public function getProvinces()
    {
        $provinces = $this->addressService->getProvinces();
        return ApiResponse::successResponse(__('success.data'), $provinces);
    }

    public function getDistricts(DistrictRequest $request)
    {
        $districts = $this->addressService->getDistricts($request->all());
        return ApiResponse::successResponse(__('success.data'), $districts);
    }

    public function getSubDistricts(SubDistrictRequest $request)
    {
        $sub = $this->addressService->getSubDistricts($request->all());
        return ApiResponse::successResponse(__('success.data'), $sub);
    }
}
