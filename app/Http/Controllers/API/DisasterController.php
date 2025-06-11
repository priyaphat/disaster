<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DisasterService;
use App\Http\Responses\ApiResponse;
use App\Models\Disaster;
use App\Http\Requests\DisasterRequest;
use App\Models\DisasterFlood;
use App\Enums\FloodStatus;
use App\Enums\SmogStatus;
use App\Models\DisasterEarthquake;
use App\Models\DisasterLandslide;
use App\Models\DisasterSmog;
use App\Models\DisasterWildfire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDO;

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


    /*####################### Disaster menu #######################*/

    //earthquake - eq
    public function dst_eq_show(Request $request, $earthquake = null)
    {
        $data = $this->disasterService->eq_show($request, $earthquake);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dst_eq_update(DisasterRequest $request, DisasterEarthquake $earthquake)
    {
        // $request['updated_by'] = Auth::id();
        // return $request->all();
        $earthquake->update($request->all());
        return ApiResponse::successResponse(__('success.sys_updated'), $earthquake);
    }

    //flood - fl
    public function dst_fl_show(Request $request, $flood = null)
    {
        $data = $this->disasterService->fl_show($request, $flood);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dst_fl_status()
    {
        $data = array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], FloodStatus::cases());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dst_fl_update(DisasterRequest $request, DisasterFlood $flood)
    {
        $flood->update($request->all());
        return ApiResponse::successResponse(__('success.sys_updated'), $flood);
    }

    //landslide
    public function dst_ls_show(Request $request, $landslide = null)
    {
        $data = $this->disasterService->ls_show($request, $landslide);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dst_ls_create(DisasterRequest $request)
    {
        $data = $this->disasterService->ls_create($request);
        return ApiResponse::successResponse(__('success.sys_created'), $data);
    }

    public function dst_ls_download($file)
    {
        return $this->disasterService->download($file);
    }

    public function dst_ls_delete(DisasterLandslide $id)
    {
        $file = DisasterLandslide::FILE . '/' . $id->file_id;
        if (Storage::exists($file)) Storage::delete($file);
        $id->delete();
        return ApiResponse::successResponse(__('success.disaster.dst_ls_delete'));
    }

    //wildfire
    public function dst_wf_show()
    {
        $data = $this->disasterService->dst_wf_show();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dst_wf_create(DisasterRequest $request)
    {
        $data = $this->disasterService->dst_wf_create($request);
        return ApiResponse::successResponse(__('success.sys_updated'), $data);
    }

    //smog
    public function dst_sm_show(Request $request, $smog = null)
    {
        $data = $this->disasterService->dst_sm_show($request, $smog);
        // if($data->isEmpty()) return  ApiResponse::notFoundResponse(__('error.model_not_found'));
        return  ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dst_sm_status()
    {
        $data = array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], SmogStatus::cases());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function dst_sm_update(DisasterRequest $request, DisasterSmog $smog)
    {
        $smog->update($request->all());
        return  ApiResponse::successResponse(__('success.data'), $smog);
    }
}
