<?php

namespace App\Services;

use App\Models\EmergencyRequest;
use App\Models\EmergencyRequestLog;
use App\Models\Prefix;
use App\Http\Utility\PaginateUtility;
use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $searchArr = ['emergency_requests.phone'];
        if (Auth::check()) {
            $searchArr = ['emergency_requests.phone', 'emergency_requests.id'];
        }
        $prefix = Prefix::all()->keyBy('id');
        $items = EmergencyRequest::query()->when(
            $search && $id == null,
            fn($q) =>
            $q->whereAny($searchArr, $search)
        )->when(
            $id,
            fn($q) =>
            $q->where('emergency_requests.id', $id)
        )
            ->with('prefix', 'disaster', 'reason', 'geography')
            ->leftJoin('hospitals as h', 'h.id', '=', 'emergency_requests.hospital_id')
            ->leftJoin('users as a', 'a.id', 'emergency_requests.accept_by')
            ->leftJoin('prefixes', 'prefixes.id', 'a.prefix_id')
            ->leftJoin('emergency_teams as et', 'et.emc_id', '=', 'emergency_requests.id')
            ->leftJoin('teams as t', 't.id', '=', 'et.team_id')
            ->groupBy('emergency_requests.id')
            ->select(
                'emergency_requests.*',
                'h.name as hospital_name',
                DB::raw("concat(prefixes.name,' ',a.firstname,' ',a.lastname) as accept_name"),
                DB::raw('COALESCE(JSON_ARRAYAGG(JSON_OBJECT("team_id", t.id, "name", t.name ,"seq_no", et.seq_no)), JSON_ARRAY()) as emergency_team')
            )
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
                $teams = json_decode($item->emergency_team, true) ?? [];

                usort($teams, function ($a, $b) {
                    return ($a['seq_no'] ?? 0) <=> ($b['seq_no'] ?? 0);
                });
                $teams = array_map(function ($team) {
                    $team['id'] = $team['team_id'];
                    unset($team['team_id']);
                    unset($team['seq_no']);
                    return $team;
                }, $teams);

                $item->emergency_team = array_values(array_filter($teams, function ($team) {
                    return $team['id'] !== null && $team['name'] !== null;
                }));

                $item->address = $item->address;
                $item->victim_name = $victimName;
                $item->emergency_team_name = $item->team?->name ?? $item->emergency_team_name;

                if ($item->source == 1) {

                    $pattern = '/ต\.(?<subdistrict>[^\s]+)\s+อ\.(?<district>[^\s]+)\s+จ\.(?<province>[^\s]+)/u';
                    $matches = [];
                    if (preg_match($pattern, $item->address, $matches)) {
                        $item->link = 'https://shingeometics.com/webgis/WebMapCRI/disasterarea/index.php?pr=' . $matches['province'] . '?amp=' . $matches['district'] . '?tb=' . $matches['subdistrict'];
                    } else {
                        $item->link = 'https://shingeometics.com/webgis/WebMapCRI/poi/index.php?name=' . $item->address . '&lat=' . $item->latitude . '&lon=' . $item->longitude;
                    }
                } else {
                    $item->link = 'https://shingeometics.com/webgis/WebMapCRI/poi/index.php?name=' . $item->address . '&lat=' . $item->latitude . '&lon=' . $item->longitude;
                }
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
        $items = EmergencyRequest::with('prefix', 'disaster', 'reason', 'geography')
            ->leftJoin('hospitals as h', 'h.id', '=', 'emergency_requests.hospital_id')
            ->leftJoin('users as a', 'a.id', 'emergency_requests.accept_by')
            ->leftJoin('prefixes', 'prefixes.id', 'a.prefix_id')
            ->leftJoin('emergency_teams as et', 'et.emc_id', '=', 'emergency_requests.id')
            ->leftJoin('teams as t', 't.id', '=', 'et.team_id')
            ->groupBy('emergency_requests.id')
            ->select(
                'emergency_requests.*',
                'h.name as hospital_name',
                DB::raw("concat(prefixes.name,' ',a.firstname,' ',a.lastname) as accept_name"),
                DB::raw('COALESCE(JSON_ARRAYAGG(JSON_OBJECT("team_id", t.id, "name", t.name ,"seq_no", et.seq_no)), JSON_ARRAY()) as emergency_team')
            );
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
            $items = $items->whereBetween('emergency_requests.created_at', [
                Carbon::parse($emc->start_date)->startOfDay(),
                Carbon::parse($emc->end_date)->endOfDay()
            ]);
        }
        if ($emc->month) $items = $items = $items->whereMonth('emergency_requests.created_at', Carbon::parse($emc->month)->month);
        if ($emc->sort) $items = $items->orderBy('emergency_requests.created_at', $emc->sort === 'asc' ? 'asc' : 'desc');
        if ($emc->sort_approve) $items = $items->orderBy('emergency_requests.updated_at', $emc->sort_approve === 'asc' ? 'asc' : 'desc');
        if ($emc->status) {
            $items = $items->where('emergency_requests.status', $emc->status);
        } else {

            if (in_array($role, [Role::TEAM_LEADER, Role::TEAM_MEMBER])) {
                $items->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('emergency_teams as et2')
                        ->join('teams as t2', 't2.id', '=', 'et2.team_id')
                        ->whereRaw('et2.emc_id = emergency_requests.id')
                        ->where('t2.id', Auth::user()->member?->team_id);
                });
            }

            if ($type == 'waiting') { // ถ้ามีทีม แสดงแค่ทีม ตัวเอง ถ้าไม่ใช่ จนท สก. รอ role
                $statusArr = [EmergencyRequest::helping];
                if ($role === Role::ADMIN || $role === Role::TEAM_LEADER) {
                    $statusArr[] = EmergencyRequest::approve;
                }

                $items = $items->whereIn('emergency_requests.status', $statusArr);
            } else if ($type == 'history') {
                $items = $items->whereIn('emergency_requests.status', [EmergencyRequest::complete, EmergencyRequest::disapprove, EmergencyRequest::incomplete, EmergencyRequest::hospital]);
            } else {
                $items = $items->where('emergency_requests.status', EmergencyRequest::waiting);
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

                $teams = json_decode($item->emergency_team, true) ?? [];

                usort($teams, function ($a, $b) {
                    return ($a['seq_no'] ?? 0) <=> ($b['seq_no'] ?? 0);
                });

                $teams = array_map(function ($team) {
                    $team['id'] = $team['team_id'];
                    unset($team['team_id']);
                    unset($team['seq_no']);
                    return $team;
                }, $teams);

                $item->emergency_team = array_values(array_filter($teams, function ($team) {
                    return $team['id'] !== null && $team['name'] !== null;
                }));

                if ($item->source == 1) {

                    $pattern = '/ต\.(?<subdistrict>[^\s]+)\s+อ\.(?<district>[^\s]+)\s+จ\.(?<province>[^\s]+)/u';
                    $matches = [];
                    if (preg_match($pattern, $item->address, $matches)) {
                        $item->link = 'https://shingeometics.com/webgis/WebMapCRI/disasterarea/index.php?pr=' . $matches['province'] . '?amp=' . $matches['district'] . '?tb=' . $matches['subdistrict'];
                    } else {
                        $item->link = 'https://shingeometics.com/webgis/WebMapCRI/poi/index.php?name=' . $item->address . '&lat=' . $item->latitude . '&lon=' . $item->longitude;
                    }
                } else {
                    $item->link = 'https://shingeometics.com/webgis/WebMapCRI/poi/index.php?name=' . $item->address . '&lat=' . $item->latitude . '&lon=' . $item->longitude;
                }

                return $item;
            });
        return PaginateUtility::Paginate($items, $request->paginate ?? 10);
    }

    public function create(array $data): EmergencyRequest
    {
        $data['range_age'] = json_encode($data['range_age']);
        $data['victim_name'] = json_encode($data['victim_name']);
        $data['source'] = $data['source'] ?? 1;
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
            $teamId = $user->member?->team_id;

            if ($teamId) {
                return EmergencyRequest::whereIn('status', [
                    EmergencyRequest::approve,
                    EmergencyRequest::helping,
                ])->whereHas('emergencyTeams', function ($query) use ($teamId) {
                    $query->where('team_id', $teamId);
                })->count();
            }
        } else if ($role == Role::TEAM_MEMBER) {
            $teamId = $user->member?->team_id;

            if ($teamId) {
                return EmergencyRequest::whereIn('status', [
                    EmergencyRequest::helping,
                ])->whereHas('emergencyTeams', function ($query) use ($teamId) {
                    $query->where('team_id', $teamId);
                })->count();
            }
        }

        return $count ?? 0;
    }
}
