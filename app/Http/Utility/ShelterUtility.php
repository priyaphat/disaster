<?php

namespace App\Http\Utility;

use App\Constants\ShelterType;
use App\Models\Shelter;
use App\Models\ShelterDailyLog;
use App\Models\ShelterPoint;
use DateTimeZone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class ShelterUtility
{
    public static function normalizeShelterName(int $shelterType, string $name): string
    {
        // Trim space
        $normalized = trim(preg_replace('/\s+/', ' ', $name));

        $type = ShelterType::findById($shelterType);
        $prefixes = collect($type['prefixes'] ?? [])->push($type['name'] ?? []);

        // Normalized name by prefix
        foreach ($prefixes as $prefix) {
            if (Str::startsWith($normalized, $prefix)) {
                $normalized = trim(Str::substr($normalized, Str::length($prefix)));
                break;
            }
        }

        return trim($normalized);
    }

    public static function normalizePointName(string $name): string
    {
        // Trim space
        $normalized = trim(preg_replace('/\s+/', ' ', $name));

        // Map all prefixes with keu
        $types = collect(ShelterType::$TYPES)->whereIn('id', [3, 5])->values();
        $types = $types->mapWithKeys(function ($item) {
            return [$item['name'] => $item['prefixes']];
        });

        foreach ($types as $key => $prefixes) {
            // Search string for matched prefix in the given string
            $prefix = collect($prefixes)
                ->first(fn($p) => Str::startsWith($normalized, $p));
            // If it has prefix, normalized key and name
            if ($prefix) {
                $name = trim(Str::substr($normalized, Str::length($prefix)));
                $normalized = Str::of($key)->append(' ')->append($name);
                break;
            }
        }

        return trim($normalized);
    }

    public static function getMaps(int $shelterType, string $name): array
    {
        $type = ShelterType::findById($shelterType);
        $normalized = Str::of($type['name'])->append(' ')->append($name)->toString();

        $point = ShelterPoint::where('original_name', $name)->first();
        if (!$point) {
            $point = ShelterPoint::where('normalized_name', $normalized)->first();
        }

        if ($point) {
            $queryString = http_build_query(['q' => Str::of($point->latitude)->append(',')->append($point->longitude)->toString()]);
            return [
                'gis_map' => $point->map_url,
                'google_maps' => Str::of(env('GOOGLE_MAPS_URL'))->append('?')->append($queryString)->toString()
            ];
        }

        return [
            'gis_map' => Str::of(env('GIS_SHELTER_URL'))->append('?')->append(http_build_query(['name' => $name]))->toString(),
            'google_maps' => Str::of(env('GOOGLE_MAPS_URL'))->append('/')->append('search')->append('/')->append(urlencode($name))->toString(),
        ];
    }

    public static function getSnapshot(?int $shelterId = null, array $filters = [])
    {
        $type = $filters['type'] ?? 1;
        $query = ShelterDailyLog::query();

        if ($shelterId) {
            $query->where('shelter_id', $shelterId);
        }

        if ($type == 1) {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $month = $filters['month'] ?? Carbon::now()->format('m');
            $day = $filters['date'] ?? Carbon::now()->format('d');
            $date = Carbon::createFromDate($year, $month, $day);
            $startDate = $date->clone()->startOfDay()->format('Y-m-d H:i:s');
            $endDate = $date->clone()->endOfDay()->format('Y-m-d H:i:s');
        } else if ($type == 2) {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $month = $filters['month'] ?? Carbon::now()->format('m');
            $fullDate = Carbon::createFromDate($year, $month, 1, config('app.timezone'));
            $startDate = $fullDate->clone()->startOfMonth()->format('Y-m-d H:i:s');
            $endDate = $fullDate->clone()->endOfMonth()->format('Y-m-d H:i:s');
        } else if ($type == 3) {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $start = $filters['start_month'] ?? Carbon::now()->format('m');
            $end = $filters['end_month'] ?? Carbon::now()->format('m');
            $startMonth = Carbon::createFromDate($year, $start);
            $endMonth = Carbon::createFromDate($year, $end);
            $startDate = $startMonth->startOfMonth();
            $endDate = $endMonth->endOfMonth();
        } else {
            $year = $filters['year'] ?? Carbon::now()->format('Y');
            $date = Carbon::createFromDate($year);
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        $baseQuery = $query->whereBetween('log_date', [$startDate, $endDate]);
        $data = $baseQuery->get()->groupBy(function ($item) {
            return Carbon::parse($item['log_date'])->format('Y-m');
        })->pipe(function ($groups) use ($shelterId, $query) {
            return $groups->map(function ($group, $key) use ($shelterId, $query) {
                return $group
                    ->groupBy(function ($log) {
                        return Carbon::parse($log->log_date)->toDateString();
                    })
                    ->map(function ($dayGroup) use ($shelterId, $query) {
                        $formatData = [
                            'title' => $dayGroup[0]['log_date'] ?? '-',
                            'total_populations' => $dayGroup->sum('total_populations'),
                            'total_injuries' => $dayGroup->sum('total_injuries'),
                            'total_healthy' => $dayGroup->sum('total_healthy'),
                            'total_deaths' => $dayGroup->sum('total_deaths'),
                        ];

                        if (!$shelterId) {
                            $formatData['total_shelter'] = $query->clone()->where(['log_date' => $dayGroup[0]['log_date'], 'status' => Shelter::OPEN])->count();
                        }

                        return $formatData;
                    })->values();
            });
        });

        return $data;
    }
}
