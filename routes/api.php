<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\PrefixController;
use App\Http\Controllers\API\ShelterController;
use App\Http\Controllers\API\EmergencyRequestController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\PopulationController;
use App\Http\Controllers\API\PositionController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserApprovalController;
use App\Http\Controllers\API\DisasterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UnitController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostController;
use App\Models\Disaster;
use App\Models\EmergencyPostLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/forgot-password',[ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'checkExpiredToken'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display positions
    |--------------------------------------------------------------------------
    */
    Route::get('/positions', [PositionController::class, 'index']);
    Route::get('/position/{position}', [PositionController::class, 'show']);
    Route::post('/position', [PositionController::class, 'store']);
    Route::post('/position/{position}', [PositionController::class, 'update']);
    Route::delete('/position/{position}', [PositionController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display departments
    |--------------------------------------------------------------------------
    */
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/department/{department}', [DepartmentController::class, 'show']);
    Route::post('/department', [DepartmentController::class, 'store']);
    Route::post('/department/{department}', [DepartmentController::class, 'update']);
    Route::delete('/department/{department}', [DepartmentController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display prefixes
    |--------------------------------------------------------------------------
    */
    Route::get('/prefixes', [PrefixController::class, 'index']);
    Route::get('/prefix/{prefix}', [PrefixController::class, 'show']);
    Route::post('/prefix', [PrefixController::class, 'store']);
    Route::post('/prefix/{prefix}', [PrefixController::class, 'update']);
    Route::delete('/prefix/{prefix}', [PrefixController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display users
    |--------------------------------------------------------------------------
    */
    Route::post('/users', [UserController::class, 'index']);
    // Route::get('/users', [UserController::class, 'list']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::post('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display user approvals
    |--------------------------------------------------------------------------
    */
    Route::post('/user-approvals', [UserApprovalController::class, 'index']);
    Route::get('/user-approval/{approval}', [UserApprovalController::class, 'show']);
    Route::post('/user-approval/{approval}', [UserApprovalController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display news
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Routes for display shelters
    |--------------------------------------------------------------------------
    */
    Route::post('/shelter', [ShelterController::class, 'store']);
    Route::post('/shelter/{shelter}', [ShelterController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display address and geography
    |--------------------------------------------------------------------------
    */
    Route::post('/address', [AddressController::class, 'getAddress']);
    Route::post('/district', [AddressController::class, 'getDistricts']);
    Route::post('/sub-district', [AddressController::class, 'getSubDistricts']);
    Route::post('/province', [AddressController::class, 'getProvinces']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display populations
    |--------------------------------------------------------------------------
    */
    Route::get('/population', [PopulationController::class, 'index']);
    Route::get('/population/{population}', [PopulationController::class, 'show']);
    Route::post('/population', [PopulationController::class, 'store']);
    Route::post('/population/{population}', [PopulationController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display units
    |--------------------------------------------------------------------------
    */
    Route::get('/unit',[UnitController::class,'index']);
    Route::post('/unit',[UnitController::class,'store']);
    Route::post('/unit/{unit}',[UnitController::class,'update']);
    Route::delete('/unit/{unit}',[UnitController::class,'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display categories
    |--------------------------------------------------------------------------
    */
    Route::get('/category',[CategoryController::class,'index']);
    Route::post('/category',[CategoryController::class,'store']);
    Route::post('/category/{category}',[CategoryController::class,'update']);
    Route::delete('/category/{category}',[CategoryController::class,'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display products
    |--------------------------------------------------------------------------
    */
    Route::get('/product',[ProductController::class,'index']);
    Route::get('/product/{product}',[ProductController::class,'show']);
    Route::post('/product',[ProductController::class,'store']);
    
    /*
    |--------------------------------------------------------------------------
    | Routes for display emergency requests
    |--------------------------------------------------------------------------
    */
    Route::get('/disaster',[DisasterController::class,'index']);
    Route::get('/disaster/{disaster}',[DisasterController::class,'show']);
    Route::post('/disaster',[DisasterController::class,'store']);
    Route::post('/disaster/{disaster}',[DisasterController::class,'update']);
    Route::delete('/disaster/{disaster}',[DisasterController::class,'destroy']);

     /*
    |--------------------------------------------------------------------------
    | Routes for display emergency post
    |--------------------------------------------------------------------------
    */
    Route::get('/post/{post}', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'show']);
    Route::post('/post', [PostController::class, 'store']);
    Route::post('/post/{post}', [PostController::class, 'update']);
    Route::delete('/post/{post}', [PostController::class, 'destroy']);

     /*
    |--------------------------------------------------------------------------
    | Routes for display emergency post log
    |--------------------------------------------------------------------------
    */
    Route::post('/post-logs', [PostController::class, 'logs']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display emergency requests
    |--------------------------------------------------------------------------
    */
    Route::post('/emc-requests', [EmergencyRequestController::class, 'show']);
    Route::post('/emc-requests-wait', [EmergencyRequestController::class, 'show_wait']);
    Route::post('/emc-requests-history', [EmergencyRequestController::class, 'show_history']);
    Route::post('/emc-request-phone', [EmergencyRequestController::class, 'emcRequestBy']);
    Route::post('/emc-request-id', [EmergencyRequestController::class, 'emcRequestBy']);
    Route::post('/emc-request', [EmergencyRequestController::class, 'store']);
    Route::post('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'update']);
    Route::delete('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display teams
    |--------------------------------------------------------------------------
    */
    Route::post('/teams', [TeamController::class, 'index']);
    // Route::get('/users', [UserController::class, 'list']);
    Route::get('/team/{team}', [TeamController::class, 'show']);
    Route::post('/team', [TeamController::class, 'store']);
    Route::post('/team/{team}', [TeamController::class, 'update']);
    Route::delete('/team/{team}', [TeamController::class, 'destroy']);
    Route::post('/emc-request/{emergencyRequest}',[EmergencyRequestController::class, 'update']);
    Route::delete('/emc-request/{emergencyRequest}',[EmergencyRequestController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display reason of emergency request
    |--------------------------------------------------------------------------
    */
    Route::get('/reason', [EmergencyRequestController::class, 'reason']);
});
