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
use App\Http\Requests\ReportRequest;
use App\Http\Resources\AssessmentsResource;
use App\Http\Utility\ReportUtility;
use App\Models\AssessmentLogs;
use App\Models\Image;
use App\Models\Population;
use App\Models\Shelter;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    use AuthorizesRequests;
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'datetime' => 'required',
            'population_id' => 'required|exists:populations,id',
            'detail' => 'nullable|array',
            'team_id' => 'required_if:type,1|exists:teams,id',
            'district_id' => 'required_if:type,1',
            'type' => 'required|in:1,2',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:102400'
        ]);

        // Permission Part
        $population = Population::findOrFail($request->population_id);
        $this->authorize('create', [Assessments::class, $population]);

        $assessment = new Assessments();
        $assessment->name = AssessmentType::tryFrom($request->type)->label();
        $assessment->shelter_id = $population->shelterPopulation?->shelter_id ?? null;
        $assessment->fill($request->all());
        $assessment->count = $this->latest($request);
        $assessment->save();

        // Upload an image to server
        if ($request->hasFile('image')) {
            $this->imageService->uploadImage($request->image, Assessments::IMAGE_FOLDER, Assessments::class, $assessment->id, Image::PRIVATE);
        }

        $res = self::index(request(), $assessment->id, 'frist');
        $this->logStore($res);
        return ApiResponse::successResponse(__('success.saved'), $res);
    }

    private function latest($req)
    {
        $latest = Assessments::where('population_id', $req['population_id'])
            ->where('type', $req['type'])
            ->latest('id')
            ->first();
        return $latest ? ($latest->count + 1) : 1;
    }

    public function index(Request $r, $id = null, $methed = null)
    {
        if (!$id) {
            $population = Population::findOrFail($r->population_id);
            $this->authorize('viewAny', [Assessments::class, $population]);
        }

        $q = Assessments::with('image')
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
            $q = new AssessmentsResource($q);
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
            $q->setCollection(AssessmentsResource::collection($q->items())->collection);
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
            'type' => 'required|in:1,2',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:102400'
        ]);

        $request['name'] = AssessmentType::tryFrom($request->type)->label();
        $assessment->fill($request->all());
        // $assessment->fill($request->except('count'));
        // $assessment->count += 1;
        $assessment->save();

        // Handle remove an image or update an image
        if (array_key_exists('image', $request->all())) {
            if ($request->hasFile('image')) {
                $this->imageService->updateImage($request->image, Assessments::IMAGE_FOLDER, Assessments::class, $assessment->id, Image::PRIVATE);
            } else {
                $this->imageService->deleteImage(Assessments::class, $assessment->id);
            }
        }

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
        $column1 = ['a' => 5, 'b' => 10, 'c' => 2, 'd' => 5, 'e' => 3, 'f' => 3];
        $i = 0;
        $selects = collect($column1)->flatMap(function ($count, $path) use (&$i) {
            return collect(range(0, $count - 1))->map(function ($j) use ($path, &$i) {
                $column = Str::of('$.assessment')->append('[' . $i . ']')->append('.')->append($path)->append($j + 1)->toString();
                $columnName = Str::of($path)->append($j + 1)->toString();
                $i++;
                return "SUM(JSON_EXTRACT(detail, '$column') = true) AS " . $columnName;
            });
        })->implode(',');
        $report = ReportUtility::getSummary($shelter?->id, $shelter?->name, $selects, 1, $request->all());

        // สรุปรายงานแบบประเมิน
        $illnessColumn = [
            'assessment_bits.illness.a1' => 5,
            'assessment_bits.illness.a2' => 10,
            'assessment_bits.illness.a3' => 2,
            'assessment_bits.illness.a4' => 5,
            'assessment_bits.care.b1' => 6,
            'assessment_bits.care.b2' => 8,
            'assessment_bits.context.c1' => 3,
            'assessment_bits.context.c2' => 4,
        ];

        $illnessSelect = collect($illnessColumn)->flatMap(function ($count, $path) {
            return collect(range(0, $count - 1))->map(function ($i) use ($path) {
                $column = Str::of('$.')->append($path)->append('[' . $i . ']')->toString();
                $exp = explode('.', $path);
                $columnName = Str::of($exp[1])->append('_')->append($exp[2])->append('_')->append($i)->toString();

                return "SUM(JSON_EXTRACT(detail, '$column')) AS " . $columnName;
            });
        })->implode(',');

        $infoColumn = [
            'assessment.breastFeeding' => 3,
            'assessment.measlesVaccinationHistory' => 3,
            'assessment.historyOfTetanusVaccination' => 3,
            'assessment.historyOfAllergies.value' => 3,
            'assessment.pastHistory.value' => 3,
            'assessment.drugHistory.value' => 3,
        ];

        $infoSelect = collect($infoColumn)->flatMap(function ($count, $path) {
            return collect(range(0, $count - 1))->map(function ($i) use ($path) {
                $column =  Str::of('$.')->append($path)->toString();
                $exp = explode('.', $path);
                $columnName = Str::of(Str::snake($exp[1]))->append('_')->append($i);
                return "SUM(CASE WHEN JSON_EXTRACT(detail, '$column') = $i THEN 1 ELSE 0 END) AS " . $columnName;
            });
        })->implode(',');

        $rangeSelect = "
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.weight')) AS DECIMAL(18,2)) BETWEEN 1 AND 40 THEN 1 ELSE 0 END) as weight_1_40,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.weight')) AS DECIMAL(18,2)) BETWEEN 41 AND 50 THEN 1 ELSE 0 END) as weight_41_50,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.weight')) AS DECIMAL(18,2)) BETWEEN 51 AND 60 THEN 1 ELSE 0 END) as weight_51_60,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.weight')) AS DECIMAL(18,2)) BETWEEN 61 AND 70 THEN 1 ELSE 0 END) as weight_61_70,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.weight')) AS DECIMAL(18,2)) BETWEEN 71 AND 80 THEN 1 ELSE 0 END) as weight_71_80,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.weight')) AS DECIMAL(18,2)) > 80  THEN 1 ELSE 0 END) AS weight_over_80,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.height')) AS DECIMAL(18,2)) BETWEEN 1 AND 100 THEN 1 ELSE 0 END) as height_1_100,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.height')) AS DECIMAL(18,2)) BETWEEN 101 AND 150 THEN 1 ELSE 0 END) as height_101_150,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.height')) AS DECIMAL(18,2)) BETWEEN 151 AND 160 THEN 1 ELSE 0 END) as height_151_160,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.height')) AS DECIMAL(18,2)) BETWEEN 161 AND 170 THEN 1 ELSE 0 END) as height_161_170,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.height')) AS DECIMAL(18,2)) BETWEEN 171 AND 180 THEN 1 ELSE 0 END) as height_171_180,
            SUM(CASE WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(detail, '$.assessment.vitalSigns.height')) AS DECIMAL(18,2)) > 180  THEN 1 ELSE 0 END) AS height_over_80
        ";

        $selects2 = Str::of($illnessSelect)->append(',')->append($infoSelect)->append(',')->append($rangeSelect);
        $report2 = ReportUtility::getSummary($shelter?->id, $shelter?->name, $selects2, 2, $request->all());

        // // Send data to the excel

        $workSheet[] = new ShelterAssessmentExport(AssessmentData::$ASSESSMENTS, $report, 'รายงานการปฏิบัติการฉุกเฉิน');
        $workSheet[] = new ShelterAssessmentExport(AssessmentData::$VISITS, $report2, 'แบบบันทึกเวชระเบียน');

        // return ['report 1' => $report, 'report2' => $report2];

        $fileName = Str::of('สรุปรายงานแบบประเมิน')->append($shelter?->name)->append('.xlsx');
        return Excel::download(new MultiSheetExport($workSheet), $fileName);
    }

    public function sync(Request $req)
    {
        return ApiResponse::successResponse(__('success.data'), $req->all());
    }
}
