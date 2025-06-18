<?php

namespace App\Services;

use App\Http\Responses\ApiResponse;
use App\Http\Utility\PaginateUtility;
use App\Models\Disaster;
use App\Models\DisasterEarthquake;
use App\Models\DisasterFlood;
use App\Models\DisasterLandslide;
use App\Models\DisasterSmog;
use App\Models\DisasterWildfire;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DisasterService
{
    public function index()
    {
        return Disaster::all();
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
            ->when($req->district, fn($q) =>
            $q->where('address', 'like', "%$req->district%"))
            ->when($id, fn($q) =>
            $q->where('id', $id))
            ->when(
                $req->subdistrict,
                fn($q) =>
                $q->where('address', 'like', "%$req->subdistrict%")
            );
        $data = $query->orderBy('created_at', $req->sort === 'asc' ? 'asc' : 'desc')->get();
        if ($id) return $data[0];
        return PaginateUtility::Paginate($data);
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
                $q->whereAny(['name', 'water_source_type'], $req->search)
            );
        $data = $q->orderBy('created_at', $req->sort === 'asc' ? 'asc' : 'desc')->get();
        if ($id) return $data[0];
        return PaginateUtility::Paginate($data);
    }

    public function ls_show($req, $id = null)
    {
        $q = DisasterLandslide::query()
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
        return PaginateUtility::Paginate($q);
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
        if (!Auth()->check()) abort(403);
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
        if ($id) return $data[0];
        return PaginateUtility::Paginate($data);
    }
}
