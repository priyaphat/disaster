<?php

namespace App\Http\Utility;

use App\Constants\ShelterType;
use App\Http\Resources\ShelterDailyLogResource;
use App\Models\Shelter;
use App\Models\ShelterDailyLog;
use App\Models\ShelterDailyTotal;
use App\Models\ShelterPoint;
use App\Services\ReportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportUtility
{
    public static function getDate(string $type, array $filters = [], $report = 'assessments')
    {
        if ($type == 1) {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $month = $filters['month'] ?? Carbon::now()->format('m');
            $day = $filters['date'] ?? Carbon::now()->format('d');
            $date = Carbon::createFromDate($year, $month, $day);
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            $title =  $date->thaidate();
        } else if ($type == 2) {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $month = $filters['month'] ?? Carbon::now()->format('m');
            $fullDate = Carbon::createFromDate($year, $month, 1, config('app.timezone'));
            $startDate = $fullDate->copy()->startOfMonth();
            $endDate = $fullDate->copy()->endOfMonth();
            $title =  $fullDate->thaidate('F Y');
        } else if ($type == 3) {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $start = $filters['start_month'] ?? Carbon::now()->format('m');
            $end = $filters['end_month'] ?? Carbon::now()->format('m');
            $startMonth = Carbon::createFromDate($year, $start);
            $endMonth = Carbon::createFromDate($year, $end);
            $startDate = $startMonth->copy()->startOfMonth();
            $endDate = $endMonth->copy()->endOfMonth();
            $title =  Str::of($startMonth->thaidate('F Y'))->append(' - ')->append($endMonth->thaidate('F Y'));
        } else {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $date = Carbon::createFromDate($year);
            $startDate = $date->copy()->startOfYear();
            $endDate = $date->copy()->endOfYear();;
            $title =  Str::of('ปี')->append($startDate->thaidate('Y'));
        }

        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        if ($report === 'assessments') {
            $data['title'] = $title;
        }

        return $data;
    }

    public static function formatShelterData(?Shelter $shelter, $group)
    {
        $isShelterExists = $shelter && $shelter->exists;
        $formatData = [
            'ไม่พบข้อมูล' => '',
            ...($isShelterExists ? ['ชื่อศูนย์พักพิง' =>  $shelter?->name ?? '-'] : []),
            ...($isShelterExists ? ['ความจุทั้งหมด' => '0'] : []),
            ...(!$isShelterExists ? ['ศูนย์พักพิง (เปิดทำการ)' => '0'] : []),
            'จำนวนผู้ประสบภัย' => '0',
            'ยอดผู้บาดเจ็บ' => '0',
            'ยอดผู้เป็นปกติ' => '0',
            'ยอดผู้เสียชีวิต' => '0',
        ];

        if ($group) {
            $title = Carbon::parse($group[0]['log_date'])->thaidate();
            $formatData = [
                $title => '',
                ...($isShelterExists ? ['ชื่อศูนย์พักพิง' =>  $shelter?->name ?? '-'] : []),
                ...($isShelterExists ? ['ความจุทั้งหมด' => $group[0]['max_capacity'] ?? '0'] : []),
                ...(!$isShelterExists ? ['ศูนย์พักพิง (เปิดทำการ)' => $group->sum('total_shelter') ?? '0'] : []),
                'จำนวนผู้ประสบภัย' => strval($group->sum('total_victims')) ?? '0',
                'ยอดผู้บาดเจ็บ' => strval($group->sum('total_injuries')) ?? '0',
                'ยอดผู้เป็นปกติ' => strval( $group->sum('total_healthy')) ?? '0',
                'ยอดผู้เสียชีวิต' => strval($group->sum('total_deaths')) ?? '0',
            ];
        }

        return $formatData;
    }


    public static function getSnapshot(?Shelter $shelter, array $filters = []): array
    {
        $data = [];
        $now = Carbon::now();
        $dateNow = $now->copy()->format('Y-m-d');
        $type = $filters['type'] ?? 1;
        $query = ShelterDailyTotal::query();

        if ($shelter && $shelter->exists) {
            $query = ShelterDailyLog::where('shelter_id', $shelter->id);
        }

        $date = self::getDate($type, $filters);
        $startDate = $date['start_date'];
        $endDate = $date['end_date'];

        if ($now->between(Carbon::parse($startDate), Carbon::parse($endDate))) {
            if ($shelter && $shelter->exists) {
                $data = new ShelterDailyLogResource($shelter);
                $data = array_merge(['date' => $dateNow], $data->toArray(request()));
                app(ReportService::class)->updateShelter($data);
            } else {
                app(ReportService::class)->updateDashboard($dateNow);
            }
        }

        $baseQuery = $query->whereBetween('log_date', [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')]);
        $data = $baseQuery->orderby('log_date')->get()->groupBy(function ($item) {
            return Carbon::parse($item['log_date'])->format('Y-m');
        })->pipe(function ($groups) use ($shelter) {
            return $groups->map(function ($group) use ($shelter) {
                return $group
                    ->groupBy(function ($log) {
                        return Carbon::parse($log->log_date)->toDateString();
                    })
                    ->map(function ($dayGroup) use ($shelter) {
                        $format = self::formatShelterData($shelter, $dayGroup);
                        return $format;
                    })->values();
            });
        })->toArray();

        return $data;
    }

    public static function getSummary(?int $shelterId = null, ?string $shelterName = null, $selects = null, $assessmentType = 1, array $filters = [])
    {
        $type = $filters['type'] ?? 1;
        $topic = $assessmentType == 1 ? 'สรุปรายงานการปฏิบัติการฉุกเฉิน' : 'สรุปรายงานเวชระเบียนของทีมปฏิบัติการฉุกเฉิน';

        $date = self::getDate($type, $filters, 'assessments');
        $title = $date['title'];
        $startDate = $date['start_date'];
        $endDate = $date['end_date'];

        $baseQuery = DB::table('assessments as a')->whereBetween('a.created_at', [$startDate, $endDate]);
        if ($shelterId) {
            $baseQuery->where('shelter_id', $shelterId);
            $title = $topic . PHP_EOL . $shelterName . PHP_EOL . $title;
        } else {
            $title = $topic . PHP_EOL . $title;
        }

        // return   $report = $baseQuery->where('type', $assessmentType)->get();
        $report = $baseQuery->where('type', $assessmentType)
            ->selectRaw($selects)
            ->first();

        $genderReport = $baseQuery
            ->join('populations as p', 'p.id', '=', 'a.population_id')
            ->select('p.gender', DB::raw('COUNT(a.id) as total_assessments'))
            ->where('type', $assessmentType)
            ->groupBy('p.gender')
            ->get();

        $report->total_questions = collect($report)->sum();
        $report->total_genders = 0;
        $report->sheet_topic = $title;
        foreach ($genderReport as $gender) {
            if ($gender->gender == 1) {
                $report->males = $gender->total_assessments;
            } else {
                $report->females = $gender->total_assessments;
            }
            $report->total_genders += $gender->total_assessments;
        }

        return $report;
    }
}
