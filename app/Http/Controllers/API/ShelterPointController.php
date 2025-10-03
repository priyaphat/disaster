<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\ShelterPoint;
use App\Services\ShelterPointService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ShelterPointController extends Controller
{
    use AuthorizesRequests;
    protected $shelterPointService;

    public function __construct(ShelterPointService $shelterPointService)
    {
        $this->shelterPointService = $shelterPointService;
    }
    /**
     * Display a listing of position in shelter| แสดงรายการตำแน่งในศูนย์พักพิง
     */
    public function search(Request $request)
    {
        $this->authorize('search', ShelterPoint::class);
        $data = $this->shelterPointService->search($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

}
