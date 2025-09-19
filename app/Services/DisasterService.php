<?php

namespace App\Services;

use App\Enums\SmogStatus;
use App\Http\Controllers\API\ConfigController;
use App\Http\Controllers\API\LineController;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LinePostUtility;
use App\Http\Utility\PaginateUtility;
use App\Models\Config;
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
        // return Disaster::select(
        //     'disasters.*',
        //     // DB::raw('IF(emergency_posts.id IS NOT NULL, TRUE, FALSE) as disable')
        //     DB::raw('IF(emergency_posts.id IS NOT NULL AND emergency_posts.status = 1, TRUE, FALSE) as disable')
        // )
        //     ->leftJoin('emergency_posts', 'disasters.id', '=', 'emergency_posts.disaster_id')
        //     ->distinct()
        //     ->get();

        return Disaster::select(
            'disasters.*',
            DB::raw('IF(SUM(emergency_posts.status = 1) > 0, 1, 0) as disable')
        )
            ->leftJoin('emergency_posts', 'disasters.id', '=', 'emergency_posts.disaster_id')
            ->groupBy('disasters.id')
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

    public function eq_show($req, $id = null, $method = null)
    {
        if ($req->groupBy) {
            $gb = DisasterEarthquake::select('latitude', 'longitude', DB::raw('MAX(id) as id'))
                ->groupBy('latitude', 'longitude')
                ->pluck('id');

            $create = Config::where('type', Config::disaster)->value('description');
            $create = $create['earthquake'];
        }

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
            )
            ->when(
                $req->groupBy,
                fn($q) =>
                $q->whereIn('id', $gb)
                    ->when(
                        $create,
                        fn($q) => $q->whereNotNull('created_by'),
                        fn($q) => $q->whereNull('created_by')
                    ),
                fn($q) =>
                $q->select('disaster_earthquakes.*', DB::raw("IF(created_by IS NOT NULL, 'เจ้าหน้าที่', 'ภายนอก') as Resources"))
            );
        $query = $query->orderBy('created_at', $req->sort === 'asc' ? 'asc' : 'desc');
        if ($method) {
            return $query->get();
        }
        if ($id) $query->with('area')->first();
        return $query->paginate($req->paginate ?? 10);
    }

    public function fl_show($req, $id = null, $method = null)
    {
        if ($req->groupBy) {
            $gb = DisasterFlood::select('disaster_floods.water_source', DB::raw('MAX(disaster_floods.id) as id'))
                ->groupBy('disaster_floods.water_source')
                ->pluck('disaster_floods.id');

            $create = Config::where('type', Config::disaster)->value('description');
            $create = $create['flood'];
        }

        $q = DisasterFlood::query()
            ->when(
                $req->district,
                fn($q) =>
                $q->where('disaster_floods.address', 'like', "%$req->district%")
            )
            ->when(
                $req->subdistrict,
                fn($q) =>
                $q->where('disaster_floods.address', 'like', "%$req->subdistrict%")
            )
            ->when(
                $req->status,
                fn($q) =>
                $q->where('disaster_floods.status', $req->status)
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('disaster_floods.id', $id)
            )
            ->when(
                $req->search,
                fn($q) =>
                $q->where(function ($query) use ($req) {
                    $query->where('disaster_floods.water_source', 'like', "%{$req->search}%");
                })
            )->when(
                $req->water_source_type,
                fn($q) => $q->Where('disaster_floods.water_source_type', $req->water_source_type)
            )->when(
                $req->externalDataSource == 1,
                fn($q) => $q->whereNull('disaster_floods.created_by'),
            )
            ->when(
                $req->externalDataSource == 2,
                fn($q) => $q->whereNotNull('disaster_floods.created_by')
            )
            ->when(
                $req->groupBy,
                fn($q) =>
                $q->whereIn('disaster_floods.id', $gb)
                    ->when(
                        $create,
                        fn($q) => $q->whereNotNull('disaster_floods.created_by'),
                        fn($q) => $q->whereNull('disaster_floods.created_by')
                    )->select('disaster_floods.*', DB::raw('concat(users.firstname," ",users.lastname) as created_name')),
                fn($q) =>
                $q->select(
                    'disaster_floods.*',
                    DB::raw("IF(disaster_floods.created_by IS NOT NULL, 'เจ้าหน้าที่', 'ภายนอก') as Resources"),
                    DB::raw('concat(users.firstname," ",users.lastname) as created_name')
                )
            );

        $q = $q->orderBy('disaster_floods.created_at', $req->sort === 'asc' ? 'asc' : 'desc')
            ->leftJoin('users', 'users.id', 'disaster_floods.created_by');
        if ($method) return $q->get();
        if ($id) return  $q->with('area')->first();
        return $q->paginate($req->paginate ?? 10);
    }

    public function ls_show($req, $id = null, $method = null)
    {
        if ($req->groupBy) {
            $gb = DisasterLandslide::select('name', DB::raw('MAX(id) as id'))
                ->groupBy('name')
                ->pluck('id');
        }

        $q = DisasterLandslide::query()
            ->when($req->start_date && $req->end_date, fn($q) =>
            $q->whereBetween('disaster_landslides.created_at', [
                Carbon::parse($req->start_date)->startOfDay(),
                Carbon::parse($req->end_date)->endOfDay()
            ]))
            ->when(
                $req->search,
                fn($q) =>
                $q->where('disaster_landslides.name', 'like', "%$req->search%")
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('disaster_landslides.id', $id)
            )
            ->when(
                $req->groupBy,
                fn($q) =>
                $q->whereIn('disaster_landslides.id', $gb)
                    ->select(
                        'disaster_landslides.*',
                        DB::raw('concat(users.firstname," ",users.lastname) as created_name')
                    ),
                fn($q) =>
                $q->select(
                    'disaster_landslides.*',
                    DB::raw("IF(disaster_landslides.created_by IS NOT NULL, 'เจ้าหน้าที่', 'ภายนอก') as Resources"),
                    DB::raw('concat(users.firstname," ",users.lastname) as created_name')
                )
            );
        $q->orderBy('disaster_landslides.updated_at', $req->sort === 'asc' ? 'asc' : 'desc')
            ->leftJoin('users', 'users.id', 'disaster_landslides.created_by');
        if ($method) $q->get();
        if ($id) $q->first();
        return $q->paginate($req->paginate ?? 10);
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

    public function dst_wf_show($request)
    {
        $data['first_graph'] = self::get_wildfire($request, DisasterWildfire::MODELI);
        $data['second_graph'] = self::get_wildfire($request, DisasterWildfire::MODELII);
        // $data['third_graph'] = self::get_wildfire(DisasterWildfire::MODELIII);
        // $data['fourth_graph'] = self::get_wildfire(DisasterWildfire::MODELIV);

        return $data;
    }

    private function get_wildfire($req, $type)
    {
        $data = DisasterWildfire::where('disaster_wildfires.type', $type)
            ->leftJoin('users', 'disaster_wildfires.created_by', 'users.id')
            ->select('disaster_wildfires.*', DB::raw('concat(users.firstname," ",users.lastname) as created_name'));
        if ($req->groupBy) {
            $create = Config::where('type', Config::disaster)->value('description');
            $create = $create['wildfire'];
            if ($create) $data = $data->whereNotNull('created_by');
            else $data = $data->whereNull('created_by');
        }
        $data = $data->latest()->first();

        if ($data) {
            $array = $data->toArray();
            unset($array['model']);
            if ($data->model) {
                $array = array_merge($array, $data->model);
            }

            // if ($type == 1) {
            //     $array['model'] = [$array['modis'] ?? null, $array['viirs'] ?? null];
            //     // unset($array['viirs'], $array['modis']);
            // }
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
        // else if ($model->type == 3) LinePostUtility::line_oa_call(LineController::Disaster_wildfire_m3, $data);
        // else if ($model->type == 4) LinePostUtility::line_oa_call(LineController::Disaster_wildfire_m4, $data);

        return $model;
    }

    public function dst_sm_show($req, $id = null, $method = null)
    {
        if ($req->groupBy) {
            $gb = DisasterSmog::select('source', DB::raw('MAX(id) as id'))
                ->groupBy('source')
                ->pluck('id');

            $create = Config::where('type', Config::disaster)->value('description');
            $create = $create['smog'];
        }

        $q = DisasterSmog::query()
            ->when(
                $req->district,
                fn($q) =>
                $q->where('disaster_smogs.address', 'like', "%$req->district%")
            )
            ->when(
                $req->subdistrict,
                fn($q) =>
                $q->where('disaster_smogs.address', 'like', "%$req->subdistrict%")
            )
            ->when(
                $req->status,
                fn($q) =>
                $q->where('disaster_smogs.status', $req->status)
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('disaster_smogs.id', $id)
                    ->with('area')
            )
            ->when(
                $req->search,
                fn($q) =>
                $q->where('disaster_smogs.source', 'like', "%$req->search%")
            )
            ->when(
                $req->groupBy,
                fn($q) =>
                $q->whereIn('disaster_smogs.id', $gb)
                    ->when(
                        $create,
                        fn($q) => $q->whereNotNull('disaster_smogs.created_by'),
                        fn($q) => $q->whereNull('disaster_smogs.created_by')
                    )->select(
                        'disaster_smogs.*',
                        DB::raw('concat(users.firstname," ",users.lastname) as created_name')
                    ),
                fn($q) =>
                $q->select(
                    'disaster_smogs.*',
                    DB::raw("IF(disaster_smogs.created_by IS NOT NULL, 'เจ้าหน้าที่', 'ภายนอก') as Resources"),
                    DB::raw('concat(users.firstname," ",users.lastname) as created_name')
                )
            );
        $data = $q->orderBy('disaster_smogs.updated_at', $req->sort === 'asc' ? 'asc' : 'desc')
            ->leftJoin('users', 'users.id', 'disaster_smogs.created_by')->get();
        $data->transform(function ($item) {
            $item->status_name = SmogStatus::tryFrom($item->status)?->label();
            return $item;
        });
        if ($method) return $data;
        if ($id) return $data[0];
        return PaginateUtility::Paginate($data, $req->paginate ?? 10);
    }
}
