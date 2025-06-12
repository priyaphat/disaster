<?php

namespace App\Services;

use App\Models\Disaster;
use App\Models\EmergencyPost;
use App\Models\EmergencyPostLog;
use App\Models\Geography;
use App\Models\RefArea;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmergencyPostService
{
    public function index($post): EmergencyPost
    {
        return $this->baseQuery()
            ->where('emergency_posts.id', $post->id)
            ->first();
    }

    public function show($post)
    {

        $data = $this->baseQuery()
            ->where('emergency_posts.status', EmergencyPost::active);
        if ($post->disaster_id) $data = $data->where('emergency_posts.disaster_id', $post->disaster_id);
        if ($post->district_id) {
            $data = $data->whereHas('areas', function ($query) use ($post) {
                $query->where('district_id', $post->district_id);
            });
        }
        if ($post->sort) $data = $data->orderBy('emergency_posts.created_at', $post->sort === 'asc' ? 'asc' : 'desc');

        return $data->get();
    }

    public function baseQuery()
    {
        return EmergencyPost::select(
            'emergency_posts.*',
            'disasters.name as disaster_name',
            'provinces.province_name_th as province_name'
        )
            ->with('areas')
            ->leftJoin('disasters', 'emergency_posts.disaster_id', '=', 'disasters.id')
            ->leftJoin(
                DB::raw('(SELECT DISTINCT province_code, province_name_th FROM geography) as provinces'),
                'emergency_posts.province_id',
                '=',
                'provinces.province_code'
            );
    }

    public function create($post): EmergencyPost
    {
        $data = EmergencyPost::create($post);
        if (isset($post['area'])) {
            foreach ($post['area'] as $area) {
                $district[] = Geography::where('district_code', $area['district_id'])->pluck('district_name_th')->first();
                $district_id[] = $area['district_id'];
                $data->areas()->create([
                    'district_id' => $area['district_id'],
                ]);
            }
        }
        $this->emergencylog_save($data, $district, $district_id);
        return $data;
    }

    public function update($post, $data): EmergencyPost
    {
        $post->update($data);
        if (isset($data['area'])) {
            $post->areas()->delete();
            foreach ($data['area'] as $area) {
                $district[] = Geography::where('district_code', $area['district_id'])->pluck('district_name_th')->first();
                $district_id[] = $area['district_id'];
                $post->areas()->create([
                    'district_id' => $area['district_id'],
                ]);
            }
        }
        $this->emergencylog_save($post, $district, $district_id);
        return $post;
    }

    public function delete($post): EmergencyPost
    {
        $post->status = EmergencyPost::inactive;
        $post->save();
        $post->areas()->delete();
        $this->emergencylog_save($post);
        return $post;
    }

    public function emergencylog_save($post, $district = null, $district_id = null)
    {

        $action = request()->route()->getActionMethod();
        $log = new EmergencyPostLog();
        $log->post_id = $post->id;
        $log->disaster_id = $post->disaster_id;

        if ($action === 'store') {
            $log->action = EmergencyPost::create;
            $description = 'ประกาศเหตุภัยพิบัติ ';
        } elseif ($action === 'update') {
            $log->action = EmergencyPost::update;
            $description = 'อัปเดตเหตุภัยพิบัติ ';
        } else {
            $log->action = EmergencyPost::delete;
            $description = 'ยุติเหตุภัยพิบัติ ';
        }
        $description .= Disaster::where('id', $post->disaster_id)->pluck('name')->first();

        if ($action === 'store' || $action === 'update') $description .= ' ระดับ ' . $post->level . ' พื้นที่ ' . implode(' ', $district);

        $log->description = $description;
        $log->district = json_encode($district_id);
        $log->created_by = $post->updated_by;
        $log->created_at = $post->updated_at;
        $log->save();
    }

    public function logs($request)
    {
        $query = EmergencyPostLog::select(
            'emergency_post_logs.*',
            DB::raw('concat_ws(" ", users.firstname, users.lastname) as created_by'),
            'disasters.name as disaster_name'
        )
            ->join('users', 'emergency_post_logs.created_by', '=', 'users.id')
            ->join('disasters', 'emergency_post_logs.disaster_id', '=', 'disasters.id');

        if ($request->name) $query = $query->where(DB::raw('concat_ws(" ", users.firstname, users.lastname)'), 'like', '%' . $request->name . '%');
        if ($request->dateStart && $request->dateEnd) {
            $start = Carbon::parse($request->dateStart)->startOfDay();
            $end = Carbon::parse($request->dateEnd)->endOfDay();
            $query = $query->whereBetween('emergency_post_logs.created_at', [$start, $end]);
        }
        if ($request->disaster_id) $query = $query->where('emergency_post_logs.disaster_id', $request->disaster_id);
        if ($request->district) $query = $query->whereJsonContains('emergency_post_logs.district', (int)$request->district);
        if ($request->action) $query = $query->where('emergency_post_logs.action', $request->action);
        return $query->get();
    }
}
