<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\ShelterController;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);



Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/provinces', [AddressController::class, 'getProvinces']);
    Route::get('/districts/{provinceId}', [AddressController::class, 'getDistricts']);
    Route::get('/sub-districts/{districtId}', [AddressController::class, 'getSubDistricts']);
    
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{news}', [NewsController::class, 'show']);
    Route::post('/news', [NewsController::class, 'store']);
    Route::post('/news/{news}', [NewsController::class, 'update']);
    Route::delete('/news/{news}', [NewsController::class, 'destroy']);

    Route::group(['middleware' => ['permission:team.view']], function () {
        Route::post('/role/create', [PermissionController::class, 'createRole']);
        Route::get('/roles', [PermissionController::class, 'showRoles']);
        Route::get('/permissions', [PermissionController::class, 'showPermissions']);
    });
    
    Route::post('/shelter', [ShelterController::class, 'store']);
    Route::post('/shelter/{shelter}', [ShelterController::class, 'update']);
    
});
