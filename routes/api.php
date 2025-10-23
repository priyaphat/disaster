<?php

use App\Console\Commands\FloodTask;
use App\Console\Commands\LineTask;
use App\Console\Commands\WeatherTask;
use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AppointmentLogController;
use App\Http\Controllers\API\AssessmentController;
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
use App\Http\Controllers\API\DamageUtilityController;
use App\Http\Controllers\API\EmergencyTeamController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\ProductRequestController;
use App\Http\Controllers\API\LineController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ProductTransferController;
use App\Http\Controllers\API\ShelterPointController;
use App\Http\Controllers\API\ShelterPopulationController;
use App\Http\Controllers\API\ShelterPositionController;
use App\Http\Controllers\API\ShelterProductController;
use App\Http\Controllers\API\ShelterStaffController;
use App\Http\Controllers\API\ShelterTypeController;
use App\Http\Controllers\API\StockLogController;
use App\Http\Controllers\API\TeamMemberController;
use App\Http\Controllers\API\TransferHistoryController;
use App\Http\Controllers\API\UserRoleController;
use App\Http\Controllers\API\VulnerableGroupController;
use App\Models\Assessments;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
// Route::get('/weatherTask', [WeatherTask::class, 'handle']);
Route::get('/line-quota', [LineTask::class, 'handle']);
Route::post('/line', [LineController::class, 'handle']);
Route::get('/line-login', function () {
    return view('line-login');
});
Route::get('/population-line', function () {
    return view('line-login-population');
});
Route::get('/line-location', function () {
    return view('location');
});

Route::post('/line-login', [LoginController::class, 'line']);
Route::post('/line-login-population', [LoginController::class, 'line_population_match']);
Route::post('/line-location', [LineController::class, 'gps']);
Route::get('/line-param/{chanel}', [LineController::class, 'chanel']);

Route::get('/image/{fileName}', [FileController::class, 'servePrivate'])->where(['fileName' => '.*'])->name('storage.private.file');
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/positions', [PositionController::class, 'index']);
Route::get('/prefixes', [PrefixController::class, 'index']);
Route::get('/emc-requests/{phone}', [EmergencyRequestController::class, 'emcRequestBy']);
Route::get('/emc-requests/{phone}/{id}', [EmergencyRequestController::class, 'emcRequestBy']);
Route::get('/disaster', [DisasterController::class, 'index']);
Route::get('/emc-requests/{phone}/{id}', [EmergencyRequestController::class, 'phone_detail']);
Route::post('/emc-request', [EmergencyRequestController::class, 'store']);
Route::get('/emc-request-select/{id?}', [EmergencyRequestController::class, 'select']);
Route::get('/dst-floods/{flood?}', [DisasterController::class, 'dst_fl_show']);
Route::get('/dst-landslides/{landslide?}', [DisasterController::class, 'dst_ls_show']);
Route::get('/dst-wildfires', [DisasterController::class, 'dst_wf_show']);
Route::get('/dst-smogs/{smog?}', [DisasterController::class, 'dst_sm_show']);
Route::get('/dst-flood-status', [DisasterController::class, 'dst_fl_status']);
Route::get('/dst-flood-source', [DisasterController::class, 'dst_fl_source']);
Route::get('/dst-smog-status', [DisasterController::class, 'dst_sm_status']);
Route::get('/dst-earthquakes/{earthquake?}', [DisasterController::class, 'dst_eq_show']);
Route::get('/dst-landslide-file/{file}', [DisasterController::class, 'dst_ls_download'])->name('download');
Route::get('/weather', [ConfigController::class, 'weather']);
Route::get('/chart/{id?}', [ConfigController::class, 'indexChart']);
Route::get('/notification', [NotificationController::class, 'notification']);
Route::get('/damaged-utilities/{type?}', [DamageUtilityController::class, 'dmgs']);
Route::get('/posts', [PostController::class, 'show']);
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/shelter', [ShelterController::class, 'index'])->middleware('check_auth_shelters_list');
Route::get('/shelter/{shelter}', [ShelterController::class, 'show'])->middleware('check_auth_shelters');
Route::get('/shelter-dashboard', [ShelterController::class, 'dashboard']);
Route::get('/news', [NewsController::class, 'index'])->middleware('check_auth_news_list');
Route::get('/new/{news}', [NewsController::class, 'show'])->middleware('check_auth_news');
Route::get('/product-requests/{RequsetId?}', [ProductRequestController::class, 'indexPublic']);
Route::get('/shelter-requests/{RequsetId}', [ProductRequestController::class, 'indexPublicDetail']);
Route::get('/export-disaster', [DisasterController::class, 'exp']);
Route::get('/export-stock', [StockLogController::class, 'exp']);

Route::get('/reports/shelter/{shelter?}', [ShelterController::class, 'export']);
Route::get('/reports/damage-utility', [DamageUtilityController::class, 'export']);
Route::get('/reports/assessments/shelter/{shelter?}', [AssessmentController::class, 'export']);
Route::get('/assessment-summary', [AssessmentController::class, 'assessment_summary']);

#region User Manual
Route::get('/download-user-doc/{type}', function ($type) {
    if ($type == 'user') {
        $path = 'guides/user';
        $file_name = '[Gentech] คู่มือใช้งาน ระบบภัยพิบัติ - เจ้าหน้าที่.pdf';
    } else {
        $path = 'guides/population';
        $file_name = '[Gentech] คู่มือใช้งาน ระบบภัยพิบัติ - ประชาชน.pdf';
    }
    if (!Storage::exists($path)) abort(404);
    return Storage::download($path, $file_name);
});
#endregion

Route::middleware('auth:api')->group(function () {

    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/logout', [LoginController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display positions
    |--------------------------------------------------------------------------
    */
    Route::get('/position/{position}', [PositionController::class, 'show']);
    Route::get('/position/{position}/deletion-check', [PositionController::class, 'checkDelete']);
    Route::post('/position', [PositionController::class, 'store']);
    Route::post('/position/{position}', [PositionController::class, 'update']);
    Route::delete('/position/{position}', [PositionController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display departments
    |--------------------------------------------------------------------------
    */
    Route::get('/department/{department}', [DepartmentController::class, 'show']);
    Route::post('/department', [DepartmentController::class, 'store']);
    Route::post('/department/{department}', [DepartmentController::class, 'update']);
    Route::get('/department/{department}/deletion-check', [DepartmentController::class, 'checkDelete']);
    Route::delete('/department/{department}', [DepartmentController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display prefixes
    |--------------------------------------------------------------------------
    */
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
    Route::get('/users/available', [UserController::class, 'selectable']); // All users
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::post('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);
    Route::post('/user-approval', [UserController::class, 'approve']);

    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/profile', [UserController::class, 'updateProfile']);

    // assign and delete role route
    Route::post('/user/{user}/role', [UserRoleController::class, 'store']);
    Route::delete('/user/{user}/role', [UserRoleController::class, 'delete']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display news
    |--------------------------------------------------------------------------
    */
    Route::post('/new', [NewsController::class, 'store']);
    Route::post('/new/{news}', [NewsController::class, 'update']);
    Route::delete('/new/{news}', [NewsController::class, 'destroy']);

    Route::post('/role', [PermissionController::class, 'store']);
    Route::get('/roles', [PermissionController::class, 'show']);
    /*
    |--------------------------------------------------------------------------
    | Routes for display shelters
    |--------------------------------------------------------------------------
    */
    Route::get('/shelter-point', [ShelterPointController::class, 'search']);
    Route::post('/shelter', [ShelterController::class, 'store']);
    Route::post('/shelter/{shelter}', [ShelterController::class, 'update']);
    Route::get('/shelter-utility', [ShelterController::class, 'utility']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display damaged utility
    |--------------------------------------------------------------------------
    */
    Route::get('/damaged-utility-name', [DamageUtilityController::class, 'dmg_name']);
    Route::get('/damaged-utility-logs', [DamageUtilityController::class, 'damaged_utility_logs']);
    Route::post('/damaged-utility', [DamageUtilityController::class, 'dmg_stroe']);
    Route::post('/damaged-utility/{id}', [DamageUtilityController::class, 'dmg_update']);
    Route::delete('/damaged-utility/{damaged}', [DamageUtilityController::class, 'dmg_delete']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display shelter staff
    |--------------------------------------------------------------------------
    */
    Route::get('/staff', [ShelterStaffController::class, 'index']);
    Route::post('/staff', [ShelterStaffController::class, 'store']);
    Route::post('/staff/transfer/{userId}', [ShelterStaffController::class, 'transfer']);
    Route::post('/staff/exit/{userId}', [ShelterStaffController::class, 'exit']);
    Route::post('/staff/exit-all', [ShelterStaffController::class, 'exitAll']);
    Route::post('/staff/assign/{userId}', [ShelterStaffController::class, 'assignPosition']);
    Route::post('/staff/join', [ShelterStaffController::class, 'join']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display shelter type positions
    |--------------------------------------------------------------------------
    */
    Route::get('/shelter-types', [ShelterTypeController::class, 'index']);
    // Route::post('/shelter-type', [ShelterTypeController::class, 'store']);
    // Route::get('/shelter-type/{shelterType}', [ShelterTypeController::class, 'show']);
    // Route::post('/shelter-type/{shelterType}', [ShelterTypeController::class, 'update']);
    // Route::delete('/shelter-type/{shelterType}', [ShelterTypeController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display shelter staff positions
    |--------------------------------------------------------------------------
    */
    Route::get('/shelter-position', [ShelterPositionController::class, 'index']);
    Route::post('/shelter-position', [ShelterPositionController::class, 'store']);
    Route::post('/shelter-position/{shelterPosition}', [ShelterPositionController::class, 'update']);
    Route::delete('/shelter-position/{shelterPosition}', [ShelterPositionController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display address and geography
    |--------------------------------------------------------------------------
    */
    Route::get('/address', [AddressController::class, 'search']);
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
    Route::get('/population/search', [PopulationController::class, 'search']);
    Route::get('/population/{population}', [PopulationController::class, 'show']);
    Route::post('/population', [PopulationController::class, 'store']);
    Route::post('/population/{population}', [PopulationController::class, 'update']);
    Route::post('/population/transfer/{population}', [ShelterPopulationController::class, 'transfer']);
    Route::post('/population/exit/{population}', [ShelterPopulationController::class, 'exit']);
    Route::post('/population-exit-all', [ShelterPopulationController::class, 'exitAll']);
    Route::post('/population-check', [PopulationController::class, 'checkPopulation']);
    Route::post('/population-add', [PopulationController::class, 'addExternalPop']);
    Route::delete('/populations', [PopulationController::class, 'destroyAll']);
    Route::delete('/population/{population}', [PopulationController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display vulnerable group
    |--------------------------------------------------------------------------
    */
    Route::get('/vulnerable-groups', [VulnerableGroupController::class, 'index']);
    Route::get('/vulnerable-groups/list', [VulnerableGroupController::class, 'selectable']); // All users
    Route::post('/vulnerable-group', [VulnerableGroupController::class, 'store']);
    Route::get('/vulnerable-group/{group}', [VulnerableGroupController::class, 'show']);
    Route::post('/vulnerable-group/{group}', [VulnerableGroupController::class, 'update']);
    Route::get('/vulnerable-group/{group}/deletion-check', [VulnerableGroupController::class, 'checkDelete']);
    Route::delete('/vulnerable-group/{group}', [VulnerableGroupController::class, 'destroy']);

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
    Route::get('/product-shelter-self', [ProductController::class, 'yourShelter']);
    Route::post('/product-quantity/{id}', [ProductController::class, 'quantity']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::post('/product/{product}', [ProductController::class, 'update']);
    Route::delete('/product/{product}', [ProductController::class, 'destroy']);
    Route::get('/product-check-del/{productId}', [ProductController::class, 'checkDel']);
    Route::get('/product-groups', [ProductController::class, 'typeName']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display products shelter
    |--------------------------------------------------------------------------
    */
    Route::get('/shelter-product/{shelter?}', [ShelterProductController::class, 'index']);
    Route::get('/shelter-all-transfer/{shelter?}', [ShelterProductController::class, 'transfer']);
    Route::get('/shelter-all-delete/{shelter?}', [ShelterProductController::class, 'delete']);
    Route::get('/shelter-one-delete/{productId}', [ShelterProductController::class, 'deleteOne']);
    Route::get('/shelter-product-excep', [ShelterProductController::class, 'productException']);
    Route::post('/shelter-product', [ShelterProductController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display products transfer
    |--------------------------------------------------------------------------
    */
    Route::get('/transfer-shelter-list', [ProductTransferController::class, 'shelterList']);
    Route::get('/transfer-product-list', [ProductTransferController::class, 'productList']);
    Route::get('/transfer-product-logs', [ProductTransferController::class, 'productLog']);
    Route::post('/transfer-product', [ProductTransferController::class, 'store']);
    Route::get('/transfer-allow', [ProductTransferController::class, 'allow']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display product requests
    |--------------------------------------------------------------------------
    */
    Route::get('/product-request', [ProductRequestController::class, 'index']);
    Route::get('/product-request-log', [ProductRequestController::class, 'requestLog']);
    Route::post('/product-request', [ProductRequestController::class, 'store']);
    Route::post('/product-request/{productReq}', [ProductRequestController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display disaster
    |-------------------------------------------------------- ------------------
    */
    Route::get('/disaster/{disaster}', [DisasterController::class, 'show']);
    Route::post('/disaster', [DisasterController::class, 'store']);
    Route::post('/disaster/{disaster}', [DisasterController::class, 'update']);
    Route::delete('/disaster/{disaster}', [DisasterController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display disaster menu
    |-------------------------------------------------------- ------------------
    */
    // Route::post('/dst-earthquake/{earthquake}', [DisasterController::class, 'dst_eq_update']); //เก็บไว้
    Route::post('/dst-earthquake', [DisasterController::class, 'dst_eq_create']);
    Route::delete('/dst-earthquake/{id}', [DisasterController::class, 'dst_eq_delete']);
    // Route::post('/dst-flood/{flood}', [DisasterController::class, 'dst_fl_update']); //เก็บไว้ก่อน
    Route::post('/dst-flood', [DisasterController::class, 'dst_fl_create']);
    Route::delete('/dst-flood/{flood}', [DisasterController::class, 'dst_fl_delete']);
    Route::post('/dst-landslide', [DisasterController::class, 'dst_ls_create']);
    Route::delete('/dst-landslide/{id}', [DisasterController::class, 'dst_ls_delete']);
    Route::post('/dst-wildfire', [DisasterController::class, 'dst_wf_create']);
    // Route::post('/dst-smog/{smog}', [DisasterController::class, 'dst_sm_update']); //เก็บไว้ก่อน
    Route::post('/dst-smog', [DisasterController::class, 'dst_sm_create']);
    Route::delete('/dst-smog/{smog}', [DisasterController::class, 'dst_sm_delete']);
    // Route::get('export', [DisasterController::class, 'exp']);


    /*
    |--------------------------------------------------------------------------
    | Routes for display emergency post
    |--------------------------------------------------------------------------
    */
    Route::get('/post/{post}', [PostController::class, 'index']);
    Route::get('/post-level', [PostController::class, 'level']);
    Route::post('/post', [PostController::class, 'store']);
    Route::post('/post/{post}', [PostController::class, 'update']);
    // Route::delete('/post/{post} ', [PostController::class, 'destroy']);

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
    Route::get('/hospitals', [EmergencyTeamController::class, 'hospitalShow']);
    Route::get('/emc-request/{id}', [EmergencyRequestController::class, 'emcRequestId']);
    Route::get('/emc-request-noti', [EmergencyRequestController::class, 'noti']);
    Route::get('/emc-requests', [EmergencyRequestController::class, 'show']);
    Route::post('/emc-requests-wait', [EmergencyRequestController::class, 'show_wait']);
    Route::post('/emc-requests-history', [EmergencyRequestController::class, 'show_history']);
    Route::post('/emc-request/{emergencyRequest}', [EmergencyRequestController::class, 'update']);
    Route::post('/emc-request-shelter', [EmergencyRequestController::class, 'requestShelter']);
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
    // Route::delete('/config/{config}', [ConfigController::class, 'destroy']);
    Route::post('/chart', [ConfigController::class, 'storeChart']);
    Route::get('/config-disaster', [ConfigController::class, 'indexDisaster']);
    Route::post('/config-disaster', [ConfigController::class, 'storeDisaster']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display teams
    |--------------------------------------------------------------------------
    */
    Route::get('/teams', [TeamController::class, 'index']);
    Route::get('/team/{id}', [TeamController::class, 'show']);
    Route::post('/team', [TeamController::class, 'store']);
    Route::post('/team/{team}', [TeamController::class, 'update']);
    Route::delete('/team/{team}', [TeamController::class, 'destroy']);
    Route::get('/team/{team}/check-appointment', [TeamController::class, 'checkUpcoming']);
    Route::post('/member/join', [TeamMemberController::class, 'join']);
    Route::post('/member/transfer', [TeamMemberController::class, 'transfer']);
    Route::post('/member/leave', [TeamMemberController::class, 'leave']);
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
    Route::get('/appointment-log/{log}', [AppointmentLogController::class, 'show']);
    Route::post('/appointment-log/{log}', [AppointmentLogController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display reason of emergency request
    |--------------------------------------------------------------------------
    */
    Route::get('/reason/{id?}', [EmergencyRequestController::class, 'reasonIndex']);
    Route::post('/reason', [EmergencyRequestController::class, 'reasonStore']);
    Route::post('/reason/{id}', [EmergencyRequestController::class, 'reasonUpdate']);
    Route::delete('/reason/{id}', [EmergencyRequestController::class, 'reasonDestroy']);

    /*
    |--------------------------------------------------------------------------
    | Routes for display transfer history
    |--------------------------------------------------------------------------
    */
    Route::get('/transfer-history/{model}/{id}', [TransferHistoryController::class, 'getLog']);
    Route::get('/shelter-history', [TransferHistoryController::class, 'shelterLog']);
    Route::get('/action-type', [TransferHistoryController::class, 'actionType']);


    /*
    |--------------------------------------------------------------------------
    | Routes for display notifications
    |--------------------------------------------------------------------------
    */
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/emc', [NotificationController::class, 'emcNotification']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications', [NotificationController::class, 'deleteAll']);
    Route::delete('/notifications/bulk', [NotificationController::class, 'bulkDelete']);

    /*
    |--------------------------------------------------------------------------
    | Routes for manage another data
    |--------------------------------------------------------------------------
    */
    Route::get('/add-all-images', [UserController::class, 'updateImage']);

    /*
    |--------------------------------------------------------------------------
    | Routes for Assessments
    |--------------------------------------------------------------------------
    */
    Route::get('/assessment/{id?}', [AssessmentController::class, 'index']);
    Route::get('/assessment-list', [AssessmentController::class, 'list']);
    Route::post('/assessment', [AssessmentController::class, 'store']);
    Route::post('/assessment/{assessment}', [AssessmentController::class, 'update']);
    Route::get('/assessment-logs/{log}', [AssessmentController::class, 'logs']);

    /*
    |--------------------------------------------------------------------------
    | Routes for Stock log
    |--------------------------------------------------------------------------
    */
    Route::get('/stocks', [StockLogController::class, 'index']);
});
