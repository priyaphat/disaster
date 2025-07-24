<?php

namespace App\Services;

use App\Models\EmergencyRequest;
use App\Enums\CancellationReason;
use App\Http\Controllers\API\LineController;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyRequestLog;
use App\Models\Prefix;
use App\Http\Utility\PaginateUtility;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class EmergencyRequestService
{
    public function index()
    {
        $data = EmergencyRequest::all();
        if ($data) {
            $data->map(function ($item) {
                $item->range_age = json_decode($item->range_age);
                $item->victim_name = json_decode($item->victim_name);
            });
        }
        return $data;
    }

    public function emcRequestBy($request, $search = null, $id = null) // id ใช้ สำหรับ get พร้อม phone public
    {
        if (!Auth::check() && !$search) {
            throw new AuthenticationException();
        }

        $searchArr = ['phone'];
        if (Auth::check()) {
            $searchArr = ['phone', 'id'];
        }

        $prefix = Prefix::all()->keyBy('id');
        $items = EmergencyRequest::query()->when(
            $search,
            fn($q) =>
            $q->whereAny($searchArr, $search)
        )->when(
            $id,
            fn($q) =>
            $q->where('id', $id)
        )
            ->with('prefix', 'disaster', 'reason', 'geography')
            ->get()
            ->map(function ($item) use ($prefix) {
                $item->range_age = json_decode($item->range_age);
                $victimName = json_decode($item->victim_name, true);
                if (is_array($victimName)) {
                    foreach ($victimName as $key => $victim) {
                        if (isset($victim['prefix'])) {
                            $victimName[$key]['prefix_name'] = $prefix[$victim['prefix']]['name'] ?? null;
                        }
                    }
                }
                $item->address = $item->address . ' ' . $item->geography->postal_code;
                $item->victim_name = $victimName;
                return $item;
            });

        if ($request->paginate) return PaginateUtility::Paginate($items);

        if (!Auth::check() && $id) {
            $items[0]['phone'] != $search;
        }
        return $items[0] ?? [];
    }

    public function show($emc, $type = null)
    {
        /* type
        null : approve=2
        waiting : waiting=1 , helping=4
        history : complete=5 , disapprove=3 , incomplete=6
        */

        $prefix = Prefix::all()->keyBy('id');
        $items = EmergencyRequest::with('prefix', 'disaster', 'reason', 'geography');
        if (($emc->search)) {
            $search = $emc->search;
            $items = $items->where(function ($query) use ($search) {
                $query->whereHas('disaster', function ($q) use ($search) { // รอเปลี่ยนเป็น team.name
                    $q->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%']);
            });
        }


        if ($emc->district_id) $items = $items->where('district_id', $emc->district_id);
        if ($emc->start_date && $emc->end_date) $items = $items->whereBetween('created_at', [$emc->start_date, $emc->end_date]);
        if ($emc->sort) $items = $items->sortBy('created_at', $emc->sort === 'asc' ? SORT_ASC : SORT_DESC); //asc or desc
        if ($emc->status) {
            $items = $items->where('status', $emc->status);
        } else {
            if ($type == 'waiting') {
                $items = $items->whereIn('status', [
                    EmergencyRequest::approve,
                    EmergencyRequest::helping
                ]);
            } elseif ($type == 'history') {
                $items = $items->whereIn('status', [EmergencyRequest::complete, EmergencyRequest::disapprove, EmergencyRequest::incomplete]);
            } else {
                $items = $items->where('status', EmergencyRequest::waiting);
            }
        }

        $items = $items->get()
            ->map(function ($item) use ($prefix) {
                $item->range_age = json_decode($item->range_age);
                $victimName = json_decode($item->victim_name, true);
                if (is_array($victimName)) {
                    foreach ($victimName as $key => $victim) {
                        if (isset($victim['prefix'])) {
                            $victimName[$key]['prefix_name'] = $prefix[$victim['prefix']]['name'] ?? null;
                        }
                    }
                }
                $item->victim_name = $victimName;
                return $item;
            });
        return PaginateUtility::Paginate($items);
    }

    public function create(array $data): EmergencyRequest
    {
        $data['range_age'] = json_encode($data['range_age']);
        $data['victim_name'] = json_encode($data['victim_name']);
        $emc = EmergencyRequest::create($data);
        if (isset($data['reason_id'])) self::created_reason($emc->id, $data);
        return $emc;
    }

    public function update(EmergencyRequest $emergencyRequest, array $data): EmergencyRequest
    {
        $data['range_age'] = json_encode($data['range_age']);
        $data['victim_name'] = json_encode($data['victim_name']);
        if ($data['status'] == EmergencyRequest::approve) $data['assigner'] = Auth::user()->id;
        $emergencyRequest->update($data);
        $emergencyRequest->refresh();
        if (isset($data['reason_id'])) self::created_reason($emergencyRequest->id, $data);
        return $emergencyRequest;
    }

    public function reason()
    {
        return array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], CancellationReason::cases());
    }

    public function created_reason($id, $data)
    {
        EmergencyRequestLog::create([
            'emergency_request_id' => $id,
            'reason_id' => $data['reason_id'],
            'reason' => $data['reason'] ?? null,
        ]);
    }

    public function noti()
    {
        $pms = Auth()->user();
        if ($pms->department_id == 1) {
            return EmergencyRequest::whereIn('status', [
                EmergencyRequest::waiting,
            ])->count();
        } else if ($pms->department_id == 2) {
            $team_id = TeamMember::where('user_id', $pms->id)
                ->leftJoin('teams', 'teams.id', '=', 'team_id')
                ->value('teams.id');

            if ($team_id) {
                return EmergencyRequest::whereIn('status', [
                    EmergencyRequest::approve,
                    EmergencyRequest::helping,
                ])->where('emergency_team_id', $team_id)->count();
            }
        }
        return 0;
    }
}
