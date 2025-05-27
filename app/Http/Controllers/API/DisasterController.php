<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DisasterService;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Disaster;
use App\Http\Requests\DisasterRequest;

class DisasterController extends Controller
{
    protected $disasterService;

    public function __construct(DisasterService $disasterService)
    {
        $this->disasterService = $disasterService;
    }

    public function index()
    {
        $data = $this->disasterService->index();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function show(Disaster $disaster)
    {
        $data = $this->disasterService->show($disaster);
        return ApiResponse::successResponse(__('success.data'), $data); 
    }

    public function store(DisasterRequest $request)
    {
        $data = $this->disasterService->create($request->all());
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    public function update(DisasterRequest $request, Disaster $disaster)
    {
        $data = $this->disasterService->update($disaster, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
    
    public function destroy(Disaster $disaster)
    {
        $disaster->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

}
