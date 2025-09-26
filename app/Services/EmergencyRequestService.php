<?php

namespace App\Services;

use App\Models\EmergencyRequest;
use App\Enums\CancellationReason;
use App\Models\EmergencyRequestLog;
use App\Models\Prefix;
use App\Http\Utility\PaginateUtility;
use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\TeamMember;
use Carbon\Carbon;
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
            ->with('prefix', 'disaster', 'reason', 'geography', 'team')
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
                $item->address = $item->address;
                $item->victim_name = $victimName;
                $item->emergency_team_name = $item->team?->name ?? $item->emergency_team_name;
                return $item;
            });

        if ($request->paginate) return PaginateUtility::Paginate($items, $request->paginate ?? 10);

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
        $role = PermissionUtility::getRoleName(Auth::user());
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
        if ($emc->start_date && $emc->end_date) {
            $items = $items->whereBetween('created_at', [
                Carbon::parse($emc->start_date)->startOfDay(),
                Carbon::parse($emc->end_date)->endOfDay()
            ]);
        }
        if ($emc->month) $items = $items = $items->whereMonth('created_at', Carbon::parse($emc->month)->month);
        if ($emc->sort) $items = $items->orderBy('created_at', $emc->sort === 'asc' ? 'asc' : 'desc');
        if ($emc->sort_approve) $items = $items->orderBy('updated_at', $emc->sort_approve === 'asc' ? 'asc' : 'desc');
        if ($emc->status) {
            $items = $items->where('status', $emc->status);
        } else {
            if ($type == 'waiting') { // ถ้ามีทีม แสดงแค่ทีม ตัวเอง ถ้าไม่ใช่ จนท สก. รอ role
                $statusArr = [EmergencyRequest::helping];
                if ($role === Role::ADMIN) {
                    $statusArr[] = EmergencyRequest::approve;
                } else if ($role === Role::TEAM_LEADER) {
                    $items->where('emergency_team_id', Auth::user()->member?->team_id);
                    $statusArr[] = EmergencyRequest::approve;
                } else if ($role === Role::TEAM_MEMBER) {
                    $items->where('emergency_team_id', Auth::user()->member?->team_id);
                }

                $items = $items->whereIn('status', $statusArr);
            } else if ($type == 'history') {
                if ($role === Role::TEAM_LEADER || $role === Role::TEAM_MEMBER) {
                    $items->where('emergency_team_id', Auth::user()->member?->team_id);
                }

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
        return PaginateUtility::Paginate($items, $request->paginate ?? 10);
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
        $user = Auth()->user();
        $role = PermissionUtility::getRoleName($user);
        if ($role == Role::ADMIN) {
            $count =  EmergencyRequest::whereIn('status', [
                EmergencyRequest::waiting,
            ])->count();
        } else if ($role == Role::TEAM_LEADER) {
            $team_id = TeamMember::where('user_id', $user->id)
                ->leftJoin('teams', 'teams.id', '=', 'team_id')
                ->value('teams.id');

            if ($team_id) {
                return EmergencyRequest::whereIn('status', [
                    EmergencyRequest::approve,
                    EmergencyRequest::helping,
                ])->where('emergency_team_id', $team_id)->count();
            }
        } else if ($role == Role::TEAM_MEMBER) {
            $team_id = TeamMember::where('user_id', $user->id)
                ->leftJoin('teams', 'teams.id', '=', 'team_id')
                ->value('teams.id');

            if ($team_id) {
                return EmergencyRequest::whereIn('status', [
                    EmergencyRequest::helping,
                ])->where('emergency_team_id', $team_id)->count();
            }
        }

        return $count ?? 0;
    }
}
