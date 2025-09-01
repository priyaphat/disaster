<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prefix\StorePrefixRequest;
use App\Http\Requests\Prefix\UpdatePrefixRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Prefix;
use App\Services\PrefixService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PrefixController extends Controller
{
    use AuthorizesRequests;
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
        $this->authorize('viewAny', Prefix::class);
        $data = $this->prefixService->getList([]);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrefixRequest $request)
    {
        //
        $this->authorize('create', Prefix::class);
        $data = $this->prefixService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prefix $prefix)
    {
        $this->authorize('view', Prefix::class);
        return ApiResponse::successResponse(__('success.data'), $prefix);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrefixRequest $request, Prefix $prefix)
    {
        //
        $this->authorize('update', Prefix::class);
        $data = $this->prefixService->update($prefix, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prefix $prefix)
    {
        //
        $this->authorize('delete', Prefix::class);
        $this->prefixService->delete($prefix);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
