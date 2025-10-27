<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmergencyTeamController extends Controller
{
    public function teamCreate($r, $o)
    {
        $team = EmergencyTeam::where('emc_id', $o['id'])->get();
        if ($team->isEmpty()) {
            foreach ($r['emergency_team'] as $i => $v) {
                EmergencyTeam::create([
                    'emc_id' => $o['id'],
                    'team_id' => $v['team_id'],
                    'seq_no' => $i + 1,
                ]);
            }
        } else {
            $team = $team->keyBy('team_id');
            foreach ($r['emergency_team'] as $i => $v) {
                if (isset($team[$v['team_id']]) && $team[$v['team_id']]->seq_no == $i + 1) {
                    $team[$v['team_id']]->update(['seq_no' => $i + 1]);
                    unset($team[$v['team_id']]);
                } else {
                    EmergencyTeam::create([
                        'emc_id' => $o['id'],
                        'team_id' => $v['team_id'],
                        'seq_no' => $i + 1,
                    ]);
                }
            }
            if ($team->isNotEmpty()) {
                EmergencyTeam::destroy($team->pluck('id')->all());
            }
        }
    }

    public function teamDel($emc_id)
    {
        EmergencyTeam::where('emc_id', $emc_id)->delete();
    }

    public function hospitalShow()
    {
        $res = DB::table('hospitals')->select('id', 'name')->where('status', 1)->get();
        return ApiResponse::successResponse(__('success.data'), $res);
    }

    public function hospitalStore($r)
    {
        $ex = DB::table('hospitals')->where('name', $r)->frist();
        if (!$ex) {
            $id = DB::table('hospitals')->insertGetId([
                'name' => $r,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return $id;
        }
        return $ex->id;
    }
}
