<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AppointmentLogController;
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
use App\Http\Controllers\API\DisasterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UnitController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ConfigController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\ProductRequestController;
use App\Http\Controllers\API\LineController;
use App\Http\Controllers\API\ShelterPositionController;
use App\Http\Controllers\API\ShelterStaffController;
use App\Http\Controllers\API\TeamMemberController;
use App\Http\Controllers\API\TransferHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'checkExpiredToken'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

/*
|--------------------------------------------------------------------------
| Routes for line
|--------------------------------------------------------------------------
*/
Route::post('/line', [LineController::class, 'handle']);
Route::get('/line-login', function () {
    return view('line-login');
});
Route::post('/line-login', [LoginController::class, 'line']);

Route::get('/private/{fileName}', [FileController::class, 'servePrivate'])->where(['fileName' => '.*'])->name('storage.private.file');

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
    Route::get('/users', [UserController::class, 'index']); // All users
    // Route::get('/users', [UserController::class, 'list']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::post('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);
    Route::post('/user-approval', [UserController::class, 'approve']);

    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    /*
    |--------------------------------------------------------------------------
    | Routes for display news
    |--------------------------------------------------------------------------
    */
    Route::get('/news', [NewsController::class, 'index']);
    Route::post('/news', [NewsController::class, 'get_all']);
    Route::get('/new/{news}', [NewsController::class, 'show']);
    Route::post('/new', [NewsController::class, 'store']);
    Route::post('/new/{news}', [NewsController::class, 'update']);
    Route::delete('/new/{news}', [NewsController::class, 'destroy']);

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
    Route::get('/shelter', [ShelterController::class, 'index']);
    Route::post('/shelter', [ShelterController::class, 'store']);
    Route::get('/shelter/{shelter}', [ShelterController::class, 'show']);
    Route::post('/shelter/{shelter}', [ShelterController::class, 'update']);
    Route::get('/shelter-utility', [ShelterController::class, 'utility']);
    /*
    |--------------------------------------------------------------------------
    | Routes for display shelter staff
    |--------------------------------------------------------------------------
    */
    Route::get('/staff', [ShelterStaffController::class, 'index']);
    Route::post('/staff', [ShelterStaffController::class, 'store']);
    Route::post('/staff/transfer/{shelterstaff}', [ShelterStaffController::class, 'transfer']);
    Route::post('/staff/exit/{shelterstaff}', [ShelterStaffController::class, 'exit']);
    Route::post('/staff/exit-all', [ShelterStaffController::class, 'exitall']);
    Route::post('/staff/assign/{shelterstaff}', [ShelterStaffController::class, 'assignposition']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display shelter staff positions
    |--------------------------------------------------------------------------
    */
    Route::get('/shelter-position', [ShelterPositionController::class, 'index']);
    Route::post('/shelter-position', [ShelterPositionController::class, 'store']);
    Route::post('/shelter-position/{shelterposition}', [ShelterPositionController::class, 'update']);
    Route::delete('/shelter-position/{shelterposition}', [ShelterPositionController::class, 'destroy']);

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
    Route::post('/population/transfer/{population}', [PopulationController::class, 'transfer']);
    Route::post('/population/exit/{population}', [PopulationController::class, 'exit']);
    Route::post('/population/exit-all', [PopulationController::class, 'exitall']);
    Route::post('/population-check', [PopulationController::class, 'checkpopulation']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display units
    |--------------------------------------------------------------------------
    */
    Route::get('/unit', [UnitController::class, 'index']);
    Route::post('/unit', [UnitController::class, 'store']);
    Route::post('/unit/{unit}', [UnitController::class, 'update']);
    Route::delete('/unit/{unit}', [UnitController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display categories
    |--------------------------------------------------------------------------
    */
    Route::get('/category', [CategoryController::class, 'index']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::post('/category/{category}', [CategoryController::class, 'update']);
    Route::delete('/category/{category}', [CategoryController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display products
    |--------------------------------------------------------------------------
    */
    Route::get('/product', [ProductController::class, 'index']);
    Route::get('/product/{product}', [ProductController::class, 'show']);
    Route::post('/product', [ProductController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display product requests
    |--------------------------------------------------------------------------
    */
    Route::get('/product-request', [ProductRequestController::class, 'index']);
    Route::post('/product-request', [ProductRequestController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display disaster
    |-------------------------------------------------------- ------------------
    */
    Route::get('/disaster', [DisasterController::class, 'index']);
    Route::get('/disaster/{disaster}', [DisasterController::class, 'show']);
    Route::post('/disaster', [DisasterController::class, 'store']);
    Route::post('/disaster/{disaster}', [DisasterController::class, 'update']);
    Route::delete('/disaster/{disaster}', [DisasterController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display disaster menu
    |-------------------------------------------------------- ------------------
    */
    Route::get('/dst-earthquakes/{earthquake?}', [DisasterController::class, 'dst_eq_show']);
    Route::post('/dst-earthquake/{earthquake}', [DisasterController::class, 'dst_eq_update']);

    Route::get('/dst-floods/{flood?}', [DisasterController::class, 'dst_fl_show']);
    Route::post('/dst-flood/{flood}', [DisasterController::class, 'dst_fl_update']);
    Route::get('/dst-flood-status', [DisasterController::class, 'dst_fl_status']);

    Route::get('/dst-landslides/{landslide?}', [DisasterController::class, 'dst_ls_show']);
    Route::post('/dst-landslide', [DisasterController::class, 'dst_ls_create']);
    Route::get('/dst-landslide-file/{file}', [DisasterController::class, 'dst_ls_download'])->name('download');
    Route::delete('/dst-landslide/{id}', [DisasterController::class, 'dst_ls_delete']);

    Route::get('dst-wildfires', [DisasterController::class, 'dst_wf_show']);
    Route::post('dst-wildfire', [DisasterController::class, 'dst_wf_create']);

    Route::get('dst-smog-status', [DisasterController::class, 'dst_sm_status']);
    Route::get('dst-smogs/{smog?}', [DisasterController::class, 'dst_sm_show']);
    Route::post('dst-smog/{smog}', [DisasterController::class, 'dst_sm_update']);

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
    Route::get('/post-logs', [PostController::class, 'logs']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display emergency requests
    |--------------------------------------------------------------------------
    */
    Route::get('/emc-requests/{emc?}', [EmergencyRequestController::class, 'emcRequestBy']);
    Route::get('/emc-request-noti', [EmergencyRequestController::class, 'noti']);
    Route::post('/emc-requests', [EmergencyRequestController::class, 'show']);
    Route::post('/emc-requests-wait', [EmergencyRequestController::class, 'show_wait']);
    Route::post('/emc-requests-history', [EmergencyRequestController::class, 'show_history']);
    Route::post('/emc-request', [EmergencyRequestController::class, 'store']);
    Route::post('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'update']);
    Route::delete('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display emergency requests
    |--------------------------------------------------------------------------
    */
    Route::get('/configs/{id?}', [ConfigController::class, 'index']);
    Route::post('/config', [ConfigController::class, 'store']);
    Route::post('/config/{config}', [ConfigController::class, 'update']);
    Route::post('/config-reset', [ConfigController::class, 'reset']);
    Route::delete('/config/{config}', [ConfigController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display teams
    |--------------------------------------------------------------------------
    */
    Route::get('/teams', [TeamController::class, 'index']);
    Route::get('/team/{team}', [TeamController::class, 'show']);
    Route::post('/team', [TeamController::class, 'store']);
    Route::post('/team/{team}', [TeamController::class, 'update']);
    Route::delete('/team/{team}', [TeamController::class, 'destroy']);
    Route::post('/member/join', [TeamMemberController::class, 'store']);
    Route::post('/member/transfer', [TeamMemberController::class, 'update']);
    Route::post('/member/leave', [TeamMemberController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display appointment
    |--------------------------------------------------------------------------
    */
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointment-daily', [AppointmentController::class, 'getDailySchedule']);
    Route::get('/appointment-month', [AppointmentController::class, 'getMonthSchedule']);
    Route::get('/appointment/{appointment}', [AppointmentController::class, 'show']);
    Route::post('/appointment', [AppointmentController::class, 'store']);
    Route::post('/appointment/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/appointment/{appointment}', [AppointmentController::class, 'destroy']);
    Route::post('/appointment/log', [AppointmentLogController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display reason of emergency request
    |--------------------------------------------------------------------------
    */
    Route::get('/reason', [EmergencyRequestController::class, 'reason']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display transfer history
    |--------------------------------------------------------------------------
    */
    Route::get('/transfer-history/{model}/{id}', [TransferHistoryController::class, 'getlog']);
    Route::get('/action-type', [TransferHistoryController::class, 'actiontype']);
});
