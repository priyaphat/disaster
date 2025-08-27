<?php

namespace App\Http\Controllers\API;

use App\Enums\AssessmentType;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Assessments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AssessmentSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'datetime' => 'required',
            'population_id' => 'required|exists:populations,id',
            'detail' => 'nullable|array',
            'team_id' => 'required_if:type,1|exists:teams,id',
            'district_id' => 'required_if:type,1',
            'type' => 'required|in:1,2,3'
        ]);
        $assessment = new Assessments();
        $assessment->name = AssessmentType::tryFrom($request->type)->label();
        $assessment->fill($request->all());
        $assessment->save();
        return ApiResponse::successResponse(__('success.saved'));
    }

    public function index(Request $r, $id = null)
    {
        $q = Assessments::query()
            ->select(
                'assessments.*',
                DB::raw('concat(users.firstname, " ", users.lastname) as created_name'),
                DB::raw("
                        IF(
                            assessments.type = 3, 
                            CONCAT(
                                JSON_UNQUOTE(JSON_EXTRACT(assessments.detail, '$.firstname')), ' ', 
                                JSON_UNQUOTE(JSON_EXTRACT(assessments.detail, '$.lastname'))
                            ),
                            CONCAT(users.firstname, ' ', users.lastname)
                        ) as name_of_assessment
                    ")
            )
            ->leftJoin('users', 'assessments.created_by', 'users.id')
            ->when($r->type, fn($q) => $q->where('assessments.type', $r->type))
            ->where('assessments.population_id', $r->population_id)
            ->orderBy('assessments.datetime', $r->sort ?? 'desc');
        if ($id) {
            $q = $q->where('assessments.id', $id)->first();
        } else {
            $q = $q->paginate($r->paginate ?? 10);
        }

        return ApiResponse::successResponse(__('success.data'), $q);
    }

    public function list(Request $request)
    {
        $q = array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], AssessmentType::cases());
        return ApiResponse::successResponse(__('success.data'), $q);
    }


    public function destroy($id)
    {
        $assessment = Assessments::find($id);
        if (!$assessment) {
            return ApiResponse::errorResponse(__('error.not_found'), 404);
        }
        $assessment->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    public function update(Request $request, Assessments $assessment)
    {
        $request->validate([
            'datetime' => 'required',
            'population_id' => 'required|exists:populations,id',
            'detail' => 'nullable|array',
            'team_id' => 'required_if:type,1|exists:teams,id',
            'district_id' => 'required_if:type,1',
            'type' => 'required|in:1,2,3'
        ]);

        $request['name'] = AssessmentType::tryFrom($request->type)->label();
        $assessment->fill($request->all());
        $assessment->save();

        return ApiResponse::successResponse(__('success.updated'), $assessment);
    }

    public function assessment_summary()
    {
        $sub = Assessments::select('population_id', DB::raw('MAX(id) as latest_id'))
            ->where('type', Assessments::Assessment)
            ->groupBy('population_id');

        $q = Assessments::joinSub($sub, 'latest', function ($join) {
            $join->on('assessments.id', '=', 'latest.latest_id');
        })
            ->select('assessments.*', 'geography.district_name_th as district_name')
            ->leftJoin('geography', 'assessments.district_id', '=', 'geography.id')
            ->get();

        $district = [];
        $summary = [];
        foreach ($q as $item) {
            foreach ($item['detail']['assessment'] as $index => $i) {
                if ($i) {
                    $district[$item['district_name']][$index] = ($district[$item['district_name']][$index] ?? 0) + 1;
                    $summary[$index] = ($summary[$index] ?? 0) + 1;
                }
            }
        }
        return Excel::download(new AssessmentSummaryExport($district, $summary), 'assessment_summary.xlsx');
        // return ApiResponse::successResponse(__('success.data'), $district, $summary);
    }
}
