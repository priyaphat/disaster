<?php

namespace App\Services;

use App\Http\Controllers\API\LineController;
use App\Http\Utility\LinePostUtility;
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
            $data = $data->whereHas('area', function ($query) use ($post) {
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
            'disasters.image as image',
            'provinces.province_name_th as province_name'
        )
            ->with('area')
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
        //data already exists -> updated same disaster_id
        $repetitive = EmergencyPost::where('disaster_id', $post['disaster_id'])
            ->where('status', EmergencyPost::inactive)
            ->first();
        if ($repetitive) {
            $post['created_at'] = $post['updated_at'] = now();
            $data = self::update($repetitive, $post);
            LinePostUtility::line_oa_call(LineController::Disaster_post, self::index($data));
            return $data;
        }

        // ประกาศ
        $data = EmergencyPost::create($post);
        if (isset($post['area'])) {
            // usort($data['area'], function ($a, $b) {
            //     return $b['district_id'] <=> $a['district_id'];
            // });

            $post['area'] = collect($post['area'])
                ->sortBy('district_id')
                ->values()
                ->toArray();

            foreach ($post['area'] as $area) {
                $district[] = Geography::where('district_code', $area['district_id'])->pluck('district_name_th')->first();
                $district_id[] = $area['district_id'];
                $data->area()->create([
                    'district_id' => $area['district_id'],
                ]);
            }
        }
        $this->emergencylog_save($data, $district, $district_id);
        LinePostUtility::line_oa_call(LineController::Disaster_post, self::index($data));
        return $data;
    }

    public function update($post, $data)
    {
        $post->update($data);
        $post->refresh();
        if (isset($data['area'])) {
            $post->area()->delete();

            usort($data['area'], function ($a, $b) {
                return $a['district_id'] <=> $b['district_id'];
            });

            foreach ($data['area'] as $area) {
                $district[] = Geography::where('district_code', $area['district_id'])->pluck('district_name_th')->first();
                $district_id[] = $area['district_id'];
                $post->area()->create([
                    'district_id' => $area['district_id'],
                ]);
            }
        }
        $this->emergencylog_save($post, $district, $district_id);
        $type['type'] = Disaster::where('id', $post['disaster_id'])->value('name');
        if ($post['status'] == EmergencyPost::inactive) {
            (new LineController)->LineNotification($type, LineController::Disaster_end);
        }
        return $post;
    }

    public function delete($post): EmergencyPost
    {
        $post->status = EmergencyPost::inactive;
        $post->save();
        $post->area()->delete();
        // $this->emergencylog_save($post);
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
            $description = __('data.post.post');
        } elseif ($action === 'update') {
            if ($post->status == EmergencyPost::active) {
                $log->action = EmergencyPost::update;
                $description = __('data.post.update');
            } else {
                $log->action = EmergencyPost::delete;
                $description = __('data.post.end');
            }
        } else {
            $log->action = EmergencyPost::delete;
            $description = __('data.post.end');
        }
        $description .= Disaster::where('id', $post->disaster_id)->pluck('name')->first();

        if ($action === 'store' || $action === 'update') $description .= ' ระดับ ' . $post->level . ' พื้นที่ ' . implode(', ', $district);

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
            ->join('disasters', 'emergency_post_logs.disaster_id', '=', 'disasters.id')
            ->orderBy('created_at', 'desc');

        if ($request->search) $query = $query->where(DB::raw('concat_ws(" ", users.firstname, users.lastname)'), 'like', '%' . $request->search . '%');
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query = $query->whereBetween('emergency_post_logs.created_at', [$start, $end]);
        }
        if ($request->disaster_id) $query = $query->where('emergency_post_logs.disaster_id', $request->disaster_id);
        if ($request->district) $query = $query->whereJsonContains('emergency_post_logs.district', (int)$request->district);
        if ($request->action) $query = $query->where('emergency_post_logs.action', $request->action); //1,2,3 c,u,d
        if ($request->paginate) {
            return $query->paginate($request->paginate ?? 10);
        }
        return $query->get();
    }
}
