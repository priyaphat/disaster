<?php

namespace App\Http\Controllers\API;

use App\Constants\AssessmentData;
use App\Enums\AssessmentType;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Assessments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AssessmentSummaryExport;
use App\Exports\MultiSheetExport;
use App\Exports\ShelterAssessmentExport;
use App\Exports\VisitAssessmentExport;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\ShelterDailyLogResource;
use App\Http\Utility\ReportUtility;
use App\Http\Utility\ShelterUtility;
use App\Models\AssessmentLogs;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterDailyLog;
use App\Services\ReportService;
use App\Services\ShelterDailyLogService;
use App\Services\ShelterServiceDailyLog;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    use AuthorizesRequests;

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

        // Permission Part
        $population = Population::findOrFail($request->population_id);
        $this->authorize('create', [Assessments::class, $population]);

        $assessment = new Assessments();
        $assessment->name = AssessmentType::tryFrom($request->type)->label();
        $assessment->shelter_id = $population->shelterPopulation?->shelter_id ?? null;
        $assessment->fill($request->all());
        $assessment->save();
        $res = self::index(request(), $assessment->id, 'frist');
        $this->logStore($res);
        return ApiResponse::successResponse(__('success.saved'), $res);
    }

    public function index(Request $r, $id = null, $methed = null)
    {
        if (!$id) {
            $population = Population::findOrFail($r->population_id);
            $this->authorize('viewAny', [Assessments::class, $population]);
        }

        $q = Assessments::query()
            ->select(
                'assessments.*',
                DB::raw('concat(up.name, " ",users.firstname, " ", users.lastname) as created_name'),
                DB::raw('concat(upd.name, " ",ud.firstname, " ", ud.lastname) as updated_name'),
                DB::raw("
                        IF(
                            assessments.type = 3, 
                                CONCAT(
                                    JSON_UNQUOTE(JSON_EXTRACT(assessments.detail, '$.assessment.c_prefix_name')), ' ', 
                                    JSON_UNQUOTE(JSON_EXTRACT(assessments.detail, '$.assessment.c_first_name')), ' ', 
                                    JSON_UNQUOTE(JSON_EXTRACT(assessments.detail, '$.assessment.c_last_name'))
                                ),
                            CONCAT(prefixes.name,' ',populations.first_name, ' ', populations.last_name)
                        ) as name_of_assessment
                    ")
            )
            ->leftJoin('populations', 'assessments.population_id', 'populations.id')
            ->leftJoin('prefixes', 'populations.prefix_id', 'prefixes.id')
            ->leftJoin('users', 'assessments.created_by', 'users.id')
            ->leftJoin('prefixes as up', 'users.prefix_id', 'up.id')
            ->leftJoin('users as ud', 'assessments.updated_by', 'ud.id')
            ->leftJoin('prefixes as upd', 'ud.prefix_id', 'upd.id');

        if ($id) {
            $q = $q->where('assessments.id', $id)->first();
            if ($methed) return $q;
        } else {
            $q = $q->where('assessments.population_id', $r->population_id)
                ->orderBy('assessments.datetime', $r->sort ?? 'desc')
                ->when(
                    $r->type,
                    fn($q) =>
                    $q->whereIn('assessments.type', $r->type)
                )->when($r['start_date'] && $r['end_date'], fn($q) =>
                $q->whereBetween('assessments.updated_at', [
                    Carbon::parse($r['start_date'])->startOfDay(),
                    Carbon::parse($r['end_date'])->endOfDay()
                ]))
                ->when($r['search'], function ($q) use ($r) {
                    $keyword = $r['search'];
                    $q->having(function ($q) use ($keyword) {
                        $q->having('name_of_assessment', 'like', "%$keyword%")
                            ->orHaving('created_name', 'like', "%$keyword%");
                    });
                });
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

        $this->authorize('delete', $assessment);

        $assessment->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    public function update(Request $request, Assessments $assessment)
    {
        $this->authorize('update', $assessment);

        $request->validate([
            'datetime' => 'required',
            'population_id' => 'required|exists:populations,id',
            'detail' => 'nullable|array',
            'team_id' => 'required_if:type,1|exists:teams,id',
            'district_id' => 'required_if:type,1',
            'type' => 'required|in:1,2,3'
        ]);

        $request['name'] = AssessmentType::tryFrom($request->type)->label();
        // $assessment->fill($request->all());
        $assessment->fill($request->except('count'));
        $assessment->count += 1;
        $assessment->save();
        $res = self::index(request(), $assessment->id, 'frist');
        $this->logStore($res);
        return ApiResponse::successResponse(__('success.updated'), $res);
    }

    private function logStore($r)
    {
        $q = new AssessmentLogs();
        $q->assessment_id = $r->id;
        $q->name_of_assessment = $r->name_of_assessment;
        $q->datetime = $r->datetime;
        $q->created_by = $r->created_by;
        $q->created_at = $r->created_at;
        $q->save();
    }

    public function logs(Request $f, $id)
    {
        $population = Population::whereHas('assessments', function ($query) use ($id) {
            $query->where('id', $id);
        })->first();
        $this->authorize('viewAny', [AssessmentLogs::class, $population]);

        $q = AssessmentLogs::select(
            'assessment_logs.*',
            DB::raw('concat(up.name, " ",users.firstname, " ", users.lastname) as created_by_name'),
            'a.name as name'
        )
            ->where('assessment_id', $id)
            ->leftJoin('assessments as a', 'assessment_logs.assessment_id', 'a.id')
            ->leftJoin('users', 'assessment_logs.created_by', 'users.id')
            ->leftJoin('prefixes as up', 'users.prefix_id', 'up.id')
            ->when($f['start_date'] && $f['end_date'], fn($q) =>
            $q->whereBetween('assessment_logs.updated_at', [
                Carbon::parse($f['start_date'])->startOfDay(),
                Carbon::parse($f['end_date'])->endOfDay()
            ]))
            ->when($f['search'], function ($q) use ($f) {
                $keyword = $f['search'];
                $q->where(function ($query) use ($keyword) {
                    $query->where('assessment_logs.name_of_assessment', 'like', "%$keyword%")
                        ->orWhere(DB::raw('concat(up.name, " ",users.firstname, " ", users.lastname)'), 'like', "%$keyword%");
                });
            })
            ->orderBy('assessment_logs.updated_at', 'desc')
            ->paginate($f['paginate'] ?? 10);
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    public function assessment_summary()
    {
        // $this->authorize('view', Assessments::class);
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

    public function export(ReportRequest $request, Shelter $shelter)
    {
        // สรุปรายงานเยี่ยมบ้าน
        $column1 = ['a' => 5, 'b' => 10, 'c' => 2, 'd' => 5, 'e' => 3, 'f' => 3];
        $i = 0;
        $selects = collect($column1)->flatMap(function ($count, $path) use (&$i) {
            return collect(range(0, $count - 1))->map(function ($j) use ($path, &$i) {
                $column = Str::of('$.assessment')->append('[' . $i . ']')->append('.')->append($path)->append($j + 1)->toString();
                $columnName = Str::of($path)->append($j+1)->toString();
                $i++;
                return "SUM(JSON_EXTRACT(detail, '$column') = true) AS " . $columnName;
            });
        })->implode(',');
        $report = ReportUtility::getSummary($shelter?->id, $shelter?->name, $selects, 1, $request->all());

        // สรุปรายงานแบบประเมิน
        $column2 = [
            'assessment_bits.illness.a1' => 5,
            'assessment_bits.illness.a2' => 10,
            'assessment_bits.illness.a3' => 2,
            'assessment_bits.illness.a4' => 5,
            'assessment_bits.care.b1' => 6,
            'assessment_bits.care.b2' => 8,
            'assessment_bits.context.c1' => 3,
            'assessment_bits.context.c2' => 4,
        ];

        $selects2 = collect($column2)->flatMap(function ($count, $path) {
            return collect(range(0, $count - 1))->map(function ($i) use ($path) {
                $column = Str::of('$.')->append($path)->append('[' . $i . ']')->toString();
                $exp = explode('.', $path);
                $columnName = Str::of($exp[1])->append('_')->append($exp[2])->append('_')->append($i)->toString();

                return "SUM(JSON_EXTRACT(detail, '$column')) AS " . $columnName;
            });
        })->implode(',');

        $report2 = ReportUtility::getSummary($shelter?->id, $shelter?->name, $selects2, 2, $request->all());

        // // Send data to the excel
        
        $workSheet[] = new ShelterAssessmentExport(AssessmentData::$ASSESSMENTS, $report, 'รายงานการปฏิบัติการฉุกเฉิน');
        $workSheet[] = new ShelterAssessmentExport(AssessmentData::$VISITS, $report2, 'แบบบันทึกเวชระเบียน');

        // return ['report 1' => $report, 'report2' => $report2];
        return Excel::download(new MultiSheetExport($workSheet), 'สรุปรายงานแบบประเมิน.xlsx');
    }
}
