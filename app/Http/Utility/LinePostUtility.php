<?php

namespace App\Http\Utility;

use App\Enums\CancellationReason;
use App\Enums\TeamPosition;
use App\Http\Controllers\API\LineController;
use App\Models\EmergencyRequestLog;
use App\Models\EmergencyTeam;
use App\Models\LineUser;
use App\Models\Prefix;
use App\Models\Product;
use App\Models\Shelter;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LinePostUtility
{
    public static function line_oa_call($form, $data)  //hotline use line checkbox
    {
        $data['line_oa'] = $data['line_oa'] ?? 0;
        if ($data['line_oa'] == 1) {
            if ($form == LineController::Disaster_post || $form == LineController::Disaster_end) {
                $data = [
                    'type' => $data['disaster_name'],
                    'level' => $data['level'],
                    'areas' => $data['gis_map'],
                    'province_name' => $data['province_name']
                ];
            }
            if (in_array($form, [
                LineController::Disaster_wildfire_m1,
                LineController::Disaster_wildfire_m2,
            ])) {
                $data = $data['model'];
            }
            return (new LineController)->LineNotification($data, $form);
        }
    }

    public static function LineMakeInformation($form, $data) //use edit data form
    {
        if ($form == LineController::Emc_request_post_team || $form == LineController::Emc_request_end) {
            $prefix = Prefix::all()->keyBy('id');

            $basequery = EmergencyTeam::where('emc_id', $data['id'])
                ->leftJoin('teams', 'emergency_teams.team_id', 'teams.id');
            $team_name = (clone $basequery)->pluck('teams.name');
            $token = (clone $basequery)
                ->leftJoin('team_members', 'teams.id', 'team_members.team_id')
                ->where('team_members.team_position', TeamPosition::LEADER)
                ->leftJoin('line_users', 'line_users.user_id', 'team_members.user_id')
                ->where('line_users.role', LineUser::User)
                ->pluck('token_id')->filter()->values();

            if ($token->isEmpty()) return;
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner = $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];
            $data = [
                'name' => $name,
                // 'team' => $team->name,
                'team' => $team_name->toArray(),
                'assigner' => $assigner ?? '-'
            ];
            return (new LineController)->LineNotification($data, $form, $token);
        } else if ($form == LineController::Emc_request_unaccept) { //ส่งให้เจ้าหน้าที่ส่วนกลาง
            $prefix = Prefix::all()->keyBy('id');

            $team = EmergencyTeam::where('emc_id', $data['id'])
                ->leftJoin('teams', 'emergency_teams.team_id', 'teams.id')
                ->select(
                    'teams.*',
                )->get();

            $team_name = $team->pluck('name')->filter()->values();

            $token = (new LineController)->getAdminLineIds();
            if (empty($token)) return;
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner =  $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];
            $data = [
                'name' => $name,
                'team' => $team_name->toArray(),
                'assigner' => $assigner ?? '-'
            ];
            return (new LineController)->LineNotification($data, $form, $token);
        } else if ($form == LineController::Emc_assigned) {
            $prefix = Prefix::all()->keyBy('id');

            $basequery = EmergencyTeam::where('emc_id', $data['id'])
                ->leftJoin('teams', 'emergency_teams.team_id', 'teams.id');
            $team = (clone $basequery)->pluck('teams.name');
            $token = (clone $basequery)
                ->leftJoin('team_members', 'teams.id', 'team_members.team_id')
                ->where('team_members.team_position', TeamPosition::MEMBER)
                ->leftJoin('line_users', 'line_users.user_id', 'team_members.user_id')
                ->where('line_users.role', LineUser::User)
                ->pluck('token_id')->filter()->values();

            if ($token->isEmpty()) return;
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner =  $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];
            $receiver = User::where('id', $data['accept_by'])->first();
            $receiver =  $prefix[$receiver['prefix_id']]['name'] . ' ' . $receiver['firstname'] . ' ' . $receiver['lastname'];
            $data = [
                'name' => $name,
                'team' => $team->toArray(),
                'assigner' => $assigner ?? '-',
                'receiver' => $receiver ?? '-',
            ];
            return (new LineController)->LineNotification($data, $form, $token);
        } else if ($form == LineController::Emc_success || $form == LineController::Emc_unsuccess || $form == LineController::Hospital) {
            $prefix = Prefix::all()->keyBy('id');

            $team = EmergencyTeam::where('emc_id', $data['id'])
                ->leftJoin('teams', 'emergency_teams.team_id', 'teams.id')->pluck('teams.name');

            $note = EmergencyRequestLog::where('emergency_request_id', $data['id'])
                ->leftJoin('cancelation_reasons as c', 'c.id', 'emergency_request_logs.reason_id')
                ->orderBy('emergency_request_logs.id', 'desc')
                ->select('c.name')
                ->value('c.name');
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner =  $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];
            $receiver = User::where('id', $data['accept_by'])->first();
            if ($receiver) {
                $receiver =  $prefix[$receiver['prefix_id']]['name'] . ' ' . $receiver['firstname'] . ' ' . $receiver['lastname'];
            }

            $raw = [
                'name' => $name,
                'team' => $team->toArray(),
                'assigner' => $assigner ?? '-',
                'receiver' => $receiver ?? '-',
                'status' => $form != LineController::Emc_unsuccess ? 'สำเร็จ' : 'ล้มเหลว',
                'note' => $form == LineController::Emc_success ? '-' : ($note ?? '-'),
            ];

            if ($form == LineController::Hospital) {
                $name = DB::table('hospitals')
                    ->where('id', $data['hospital_id'])
                    ->value('name');
                $raw['note'] = 'ส่งสถานพยาบาล ' . $name;
            }
            $token = (new LineController)->getAdminLineIds();
            return (new LineController)->LineNotification($raw, $form, $token);
        } else if ($form == LineController::User_on || $form == LineController::User_off) {
            $recepint = LineUser::whereIn('user_id', $data['user_id'])
                ->where('role', LineUser::User)->pluck('token_id');
            $shelter = Shelter::where('id', $data['shelter_id'])->value('name');
            if ($recepint->isEmpty()) return;
            return (new LineController)->LineNotification([
                'shelter' => $shelter
            ], $form, $recepint);
        } else if ($form == LineController::User_join || $form == LineController::User_leave) {
            $recepint = LineUser::where('user_id', $data['user_id'])
                ->where('role', LineUser::User)->pluck('token_id');
            $team = Team::where('id', $data['team_id'])->value('name');
            if ($recepint->isEmpty()) return;
            return (new LineController)->LineNotification([
                'team' => $team
            ], $form, $recepint);
        } else if ($form == LineController::Resource) {
            $q['shelter'] = Shelter::where('id', ProductUtility::getShelter())->value('name');
            foreach ($data as $i) {
                $q['product'][]  = Product::where('id', $i['product_id'])->value('name');
            }
            $data = $q;
        }

        return (new LineController)->LineNotification($data, $form);
    }
}
