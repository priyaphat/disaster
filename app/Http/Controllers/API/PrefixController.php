<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prefix\StorePrefixRequest;
use App\Http\Requests\Prefix\UpdatePrefixRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Prefix;
use App\Services\PrefixService;

class PrefixController extends Controller
{
    protected $prefixService;

    public function __construct(PrefixService $prefixService)
    {
        $this->prefixService = $prefixService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = $this->prefixService->getList([]);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrefixRequest $request)
    {
        //
        $data = $this->prefixService->create($request->all());
        return ApiResponse::successResponse(__('success.created'), $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prefix $prefix)
    {
        return ApiResponse::successResponse(__('success.data'), $prefix);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrefixRequest $request, Prefix $prefix)
    {
        //
        $data = $this->prefixService->update($prefix, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prefix $prefix)
    {
        //
        $this->prefixService->delete($prefix);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
