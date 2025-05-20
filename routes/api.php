<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\PrefixController;
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

Route::get('/provinces', [AddressController::class, 'getProvinces']);
Route::get('/amphures/{provinceId}', [AddressController::class, 'getAmphures']);
Route::get('/tambons/{amphureId}', [AddressController::class, 'getTambons']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/department/{department}', [DepartmentController::class, 'show']);
    Route::post('/department', [DepartmentController::class, 'store']);
    Route::post('/department/{department}', [DepartmentController::class, 'update']);
    Route::delete('/department/{department}', [DepartmentController::class, 'destroy']);

    Route::get('/prefixes', [PrefixController::class, 'index']);
    Route::get('/prefix/{prefix}', [PrefixController::class, 'show']);
    Route::post('/prefix', [PrefixController::class, 'store']);
    Route::post('/prefix/{prefix}', [PrefixController::class, 'update']);
    Route::delete('/prefix/{prefix}', [PrefixController::class, 'destroy']);

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
