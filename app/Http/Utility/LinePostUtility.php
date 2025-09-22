<?php

namespace App\Http\Utility;

use App\Enums\CancellationReason;
use App\Enums\DamagedUtility;
use App\Enums\TeamPosition;
use App\Http\Controllers\API\LineController;
use App\Models\EmergencyRequestLog;
use App\Models\LineUser;
use App\Models\Prefix;
use App\Models\Product;
use App\Models\Shelter;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                    'areas' => $data['gis_map']
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
            $team = Team::select(
                'teams.*',
                'team_members.user_id',
                DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
                'line_users.token_id'
            )
                ->where('teams.id', $data['emergency_team_id'])
                ->leftJoin('team_members', 'teams.id', 'team_members.team_id')
                ->where('team_members.team_position', TeamPosition::LEADER)
                ->leftJoin('users', 'users.id', 'team_members.user_id')
                ->leftJoin('line_users', 'line_users.user_id', 'team_members.user_id')
                ->first();

            if ($team['token_id'] == null) return;
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner =  $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];
            $data = [
                'name' => $name,
                'team' => $team->name,
                'assigner' => $assigner ?? '-'
            ];
            return (new LineController)->LineNotification($data, $form, [$team->token_id]);
        } else if ($form == LineController::Emc_request_unaccept) { //ส่งให้เจ้าหน้าที่ส่วนกลาง
            $prefix = Prefix::all()->keyBy('id');
            $team = Team::select(
                'teams.*',
                'team_members.user_id',
                DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
                'line_users.token_id'
            )
                ->where('teams.id', $data['emergency_team_id'])
                ->leftJoin('team_members', 'teams.id', 'team_members.team_id')
                ->where('team_members.team_position', TeamPosition::LEADER)
                ->leftJoin('users', 'users.id', 'team_members.user_id')
                ->leftJoin('line_users', 'line_users.user_id', 'team_members.user_id')
                ->first();
            if ($team->token_id == null) return;
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner =  $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];
            $data = [
                'name' => $name,
                'team' => $team->name,
                'assigner' => $assigner ?? '-'
            ];
        } else if ($form == LineController::Emc_assigned) {
            $prefix = Prefix::all()->keyBy('id');
            $basequery = Team::where('teams.id', $data['emergency_team_id']);

            $line = (clone $basequery)->select(
                'teams.*',
                'team_members.user_id',
                DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
                'line_users.token_id'
            )->leftJoin('team_members', 'teams.id', 'team_members.team_id')
                ->where('team_members.team_position', TeamPosition::MEMBER)
                ->leftJoin('users', 'users.id', 'team_members.user_id')
                ->leftJoin('line_users', 'line_users.user_id', 'team_members.user_id')
                ->pluck('line_users.token_id');
            $team = $basequery->value('name');
            $receiver = $basequery->select(
                'teams.*',
                'team_members.user_id',
                DB::raw('concat(users.firstname," ",users.lastname) as user_name')
            )->leftJoin('team_members', 'teams.id', 'team_members.team_id')
                ->where('team_members.team_position', TeamPosition::LEADER)
                ->leftJoin('users', 'users.id', 'team_members.user_id')
                ->value('user_name');

            if ($line->isEmpty()) return;
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner =  $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];
            $data = [
                'name' => $name,
                'team' => $team,
                'assigner' => $assigner ?? '-',
                'receiver' => $receiver ?? '-',
            ];
            return (new LineController)->LineNotification($data, $form, $line);
        } else if ($form == LineController::Emc_success || $form == LineController::Emc_unsuccess) {
            $prefix = Prefix::all()->keyBy('id');
            $basequery = Team::where('teams.id', $data['emergency_team_id']);

            $team = (clone $basequery)->value('name');
            $receiver = (clone $basequery)->select(
                'teams.*',
                'team_members.user_id',
                DB::raw('concat(users.firstname," ",users.lastname) as user_name')
            )->leftJoin('team_members', 'teams.id', 'team_members.team_id')
                ->where('team_members.team_position', TeamPosition::LEADER)
                ->leftJoin('users', 'users.id', 'team_members.user_id')
                ->value('user_name');
            $note = EmergencyRequestLog::where('emergency_request_id', $data['id'])->orderBy('id', 'desc')->value('reason_id');
            $name = $prefix[$data['prefix_id']]['name'] . ' ' . $data['first_name'] . ' ' . $data['last_name'];
            $assigner = User::where('id', $data['assigner'])->first();
            $assigner =  $prefix[$assigner['prefix_id']]['name'] . ' ' . $assigner['firstname'] . ' ' . $assigner['lastname'];

            $data = [
                'name' => $name,
                'team' => $team,
                'assigner' => $assigner ?? '-',
                'receiver' => $receiver ?? '-',
                'status' => $form == LineController::Emc_success ? 'สำเร็จ' : 'ล้มเหลว',
                'note' => $form == LineController::Emc_success ? '-' : (CancellationReason::tryFrom($note) ? CancellationReason::tryFrom($note)->label() : '-'),
            ];
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
