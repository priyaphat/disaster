<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\PrefixController;
use App\Http\Controllers\API\ShelterController;
use App\Http\Controllers\API\EmergencyRequestController;
use App\Http\Controllers\API\PositionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
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

    Route::get('/positions', [PositionController::class, 'index']);
    Route::get('/position/{position}', [PositionController::class, 'show']);
    Route::post('/position', [PositionController::class, 'store']);
    Route::post('/position/{position}', [PositionController::class, 'update']);
    Route::delete('/position/{position}', [PositionController::class, 'destroy']);

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

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::post('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);

    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{news}', [NewsController::class, 'show']);
    Route::post('/news', [NewsController::class, 'store']);
    Route::post('/news/{news}', [NewsController::class, 'update']);
    Route::delete('/news/{news}', [NewsController::class, 'destroy']);

    Route::post('/role', [PermissionController::class, 'createRole']);

    Route::group(['middleware' => ['permission:team.view']], function () {
        Route::get('/roles', [PermissionController::class, 'showRoles']);
        Route::get('/permissions', [PermissionController::class, 'showPermissions']);
    });

    Route::post('/shelter', [ShelterController::class, 'store']);
    Route::post('/shelter/{shelter}', [ShelterController::class, 'update']);


    Route::get('/emc-request', [EmergencyRequestController::class, 'index']);
    Route::get('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'show']);
    Route::post('/emc-request', [EmergencyRequestController::class, 'store']);
    Route::post('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'update']);
    Route::delete('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'destroy']);
});
