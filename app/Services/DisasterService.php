<?php

namespace App\Services;

use App\Enums\SmogStatus;
use App\Http\Controllers\API\LineController;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LinePostUtility;
use App\Http\Utility\PaginateUtility;
use App\Models\Disaster;
use App\Models\DisasterEarthquake;
use App\Models\DisasterFlood;
use App\Models\DisasterLandslide;
use App\Models\DisasterSmog;
use App\Models\DisasterWildfire;
use App\Models\EmergencyPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DisasterService
{
    public function index()
    {
        //check disaster name use
        return Disaster::select(
            'disasters.*',
            DB::raw('IF(emergency_posts.id IS NOT NULL, TRUE, FALSE) as disable')
        )
            ->leftJoin('emergency_posts', 'disasters.id', '=', 'emergency_posts.disaster_id')
            ->get();
    }

    public function show(Disaster $disaster): Disaster
    {
        return $disaster;
    }

    public function create(array $data): Disaster
    {
        return Disaster::create($data);
    }

    public function update(Disaster $disaster, array $data): Disaster
    {
        $disaster->update($data);
        return $disaster;
    }

    /* Disaster menu */

    public function eq_show($req, $id = null)
    {
        $query = DisasterEarthquake::query()
            ->when($req->start_date && $req->end_date, fn($q) =>
            $q->whereBetween('created_at', [
                Carbon::parse($req->start_date)->startOfDay(),
                Carbon::parse($req->end_date)->endOfDay()
            ]))
            ->when($req->start_date, fn($q) =>
            $q->whereDate(
                'created_at',
                Carbon::parse($req->start_date),
            ))
            ->when(
                $req->district,
                fn($q) =>
                $q->where('address', 'like', "%$req->district%")
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('id', $id)
            )
            ->when(
                $req->subdistrict,
                fn($q) =>
                $q->where('address', 'like', "%$req->subdistrict%")
            );
        $data = $query->orderBy('created_at', $req->sort === 'asc' ? 'asc' : 'desc')->get();
        if ($id) return $data[0];
        return PaginateUtility::Paginate($data, $req->paginate ?? 10);
    }

    public function fl_show($req, $id = null)
    {
        $q = DisasterFlood::query()
            ->when(
                $req->district,
                fn($q) =>
                $q->where('address', 'like', "%$req->district%")
            )
            ->when(
                $req->subdistrict,
                fn($q) =>
                $q->where('address', 'like', "%$req->subdistrict%")
            )
            ->when(
                $req->status,
                fn($q) =>
                $q->where('status', $req->status)
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('id', $id)
            )
            ->when(
                $req->search,
                fn($q) =>
                $q->whereAny(['water_source', 'water_source_type'], $req->search)
            );
        $data = $q->orderBy('created_at', $req->sort === 'asc' ? 'asc' : 'desc')->get();
        if ($id) return $data[0];
        return PaginateUtility::Paginate($data, $req->paginate ?? 10);
    }

    public function ls_show($req, $id = null)
    {
        $q = DisasterLandslide::query()
            ->when($req->start_date && $req->end_date, fn($q) =>
            $q->whereBetween('created_at', [
                Carbon::parse($req->start_date)->startOfDay(),
                Carbon::parse($req->end_date)->endOfDay()
            ]))
            ->when(
                $req->search,
                fn($q) =>
                $q->where('name', 'like', "%$req->search%")
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('id', $id)
            );
        $q = $q->orderBy('updated_at', $req->sort === 'asc' ? 'asc' : 'desc')->get();
        if ($id) return $q[0];
        return PaginateUtility::Paginate($q, $req->paginate ?? 10);
    }

    public function ls_create($r)
    {
        // $path = $request->file('file')->store('landslides');
        $file = $r->file('file');
        $file_name = $file->getClientOriginalName();
        $path = $file->store(DisasterLandslide::FILE);

        $r['file_name'] = $file_name;
        $r['file_id'] = basename($path);
        $r['link'] = route('download', basename($path));
        return DisasterLandslide::create($r->all());
    }

    public function download($file)
    {
        $file_name = DisasterLandslide::where('file_id', $file)->firstOrFail()->file_name;
        $path = DisasterLandslide::FILE . '/' . $file;
        // if (!Auth()->user()) abort(403);
        if (!Storage::exists($path)) abort(404);
        return Storage::download($path, $file_name);
    }

    public function dst_wf_show()
    {
        $data['first_graph'] = self::get_wildfire(DisasterWildfire::MODELI);
        $data['second_graph'] = self::get_wildfire(DisasterWildfire::MODELII);
        $data['third_graph'] = self::get_wildfire(DisasterWildfire::MODELIII);
        $data['fourth_graph'] = self::get_wildfire(DisasterWildfire::MODELIV);

        return $data;
    }

    private function get_wildfire($type)
    {
        $data = DisasterWildfire::where('type', $type)->latest()->first();
        if ($data) {
            $array = $data->toArray();
            unset($array['model']);
            if ($data->model) {
                $array = array_merge($array, $data->model);
            }

            if ($type == 1) {
                $array['model'] = [$array['modis'] ?? null, $array['viirs'] ?? null];
                // unset($array['viirs'], $array['modis']);
            }
        }
        return $array ?? null;
    }

    public function dst_wf_create($request)
    {
        $data = $request->all();
        $type = $data['type'];
        $other = collect($data)->except('type')->toArray();
        $model = new DisasterWildfire();
        $model->type = $type;
        $model->model = $other;
        $model->save();

        if ($model->type == 1) LinePostUtility::line_oa_call(LineController::Disaster_wildfire_m1, $data);
        else if ($model->type == 2) LinePostUtility::line_oa_call(LineController::Disaster_wildfire_m2, $data);
        else if ($model->type == 3) LinePostUtility::line_oa_call(LineController::Disaster_wildfire_m3, $data);
        else if ($model->type == 4) LinePostUtility::line_oa_call(LineController::Disaster_wildfire_m4, $data);

        return $model;
    }

    public function dst_sm_show($req, $id = null)
    {
        $q = DisasterSmog::query()
            ->when(
                $req->district,
                fn($q) =>
                $q->where('address', 'like', "%$req->district%")
            )
            ->when(
                $req->subdistrict,
                fn($q) =>
                $q->where('address', 'like', "%$req->subdistrict%")
            )
            ->when(
                $req->status,
                fn($q) =>
                $q->where('status', $req->status)
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('id', $id)
            )
            ->when(
                $req->search,
                fn($q) =>
                $q->where('source', 'like', "%$req->search%")
            );
        $data = $q->orderBy('updated_at', $req->sort === 'asc' ? 'asc' : 'desc')->get();
        $data->transform(function ($item) {
            $item->status_name = SmogStatus::tryFrom($item->status)?->label();
            return $item;
        });
        if ($id) return $data[0];
        return PaginateUtility::Paginate($data, $req->paginate ?? 10);
    }
}
