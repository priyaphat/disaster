<?php

namespace App\Services;

use App\Enums\ShiftType;
use App\Http\Resources\AppointmentResource;
use App\Http\Utility\ConvertUtility;
use App\Http\Utility\LogUtility;
use App\Models\Appointment;
use App\Utility\GvarFunc;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Str;

class AppointmentService
{
    /**
     * Get all Appointmentes with filter and limit number.
     */
    public function index(array $filters = [])
    {
        $data = [];
        $filters['type'] = 1;

        $todayAppointment = $this->getDailySchedule($filters);
        $monthAppointment = $this->getMonthSchedule($filters);

        $data['today_schedules'] = $todayAppointment;
        $data['month_schedules'] = $monthAppointment;
        return $data;
    }

    /**
     * Get calendar detail.
     */
    public function getDailySchedule(array $filters = [])
    {
        $filters['selected_date'] = isset($filters['selected_date']) ? Carbon::parse($filters['selected_date'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $shiftData = Appointment::filter($filters)->with('team', 'shelter', 'teamLog', 'shelterLog')->oldest('start_time')->get();

        $data = $shiftData->sortBy('start_time')->groupBy('shift')->mapWithKeys(function ($recordsByShift, $shift) use ($filters) {
            $shiftData = [
                'shift' => $shift,
                'shift_name' => ShiftType::tryFrom($shift)->label(),
                'total_team' => $recordsByShift->groupBy('team_id')->count(),
                'total_shelter' => $recordsByShift->groupBy('shelter_id')->count(),
                'appointments' => $recordsByShift->map(function ($record) {
                    return new AppointmentResource($record);
                })->values(),
            ];
            return [$shift == 1 ? 'morning' : 'afternoon' => $shiftData = $this->resolveEntity($shiftData, $filters)];
        });

        $data = $this->normalizeShiftGroups($data);
        return $data;
    }

    /**
     * Get month schedule.
     */
    public function getMonthSchedule(array $filters = [])
    {
        $filters['type'] = isset($filters['type']) ? ConvertUtility::toInt($filters['type']) : 1;
        // If request has date, unset month.
        if (isset($filters['selected_date']) && $filters['selected_date'] && $filters['type'] != 1) {
            $filters['selected_date'] = Carbon::createFromFormat('Y-m-d',  $filters['selected_month'] . '-' . $filters['selected_date'])->format('Y-m-d');
            unset($filters['selected_month']);
        } else {
            $filters['selected_month'] = isset($filters['selected_month']) ? $filters['selected_month'] : Carbon::now()->format('Y-m');
            unset($filters['selected_date']);
        }

        if ($filters['type'] == 1) {
            $monthAppointment = $this->getCalendar($filters);
        } else {
            $monthAppointment = $this->getTimeTables($filters);
        }
        return $monthAppointment;
    }

    /**
     * Get calendar detail.
     */
    public function getCalendar($filters)
    {
        $shiftData = Appointment::filter($filters)->get();
        $data = $shiftData->groupBy('appointment_date')
            ->map(function ($recordsByDate, $date) use ($filters) {
                $shiftData = [
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                    'total_team' => $recordsByDate->groupBy('team_id')->count(),
                    'total_shelter' => $recordsByDate->groupBy('shelter_id')->count()
                ];
                return $this->resolveEntity($shiftData, $filters);
            })->values();

        return $data;
    }

    /**
     * Get time tables.
     */
    public function getTimeTables($filters)
    {
        $monthAppointment = Appointment::with(
            'team',
            'shelter',
            'team.allUsers',
            'teamLog',
            'shelterLog',
        )->filter($filters)->oldest('appointment_date', 'start_time')->get();


        $tranformedData = AppointmentResource::collection($monthAppointment)->toArray(request());

        $search = isset($filters['search']) ? $filters['search'] : null;
        if ($search) {
            $tranformedData = collect($tranformedData)->filter(function ($item) use ($search) {
                return Str::contains($item['team_name'], $search) || Str::contains($item['shelter_name'], $search);
            });
        }

        $tranformedData = collect($tranformedData)->groupBy('appointment_date')
            ->map(function ($recordsByDate, $date) use ($filters) {
                $shiftData = $recordsByDate->sortBy('start_time')->groupBy('shift')->mapWithKeys(function ($recordsByShift, $shift) use ($filters) {
                    $shiftData = [
                        'shift' => $shift,
                        'shift_name' => ShiftType::tryFrom($shift)->label(),
                        'total_team' => $recordsByShift->groupBy('team_id')->count(),
                        'total_shelter' => $recordsByShift->groupBy('shelter_id')->count(),
                        'appointments' => $recordsByShift
                    ];
                    return [$shift == 1 ? 'morning' : 'afternoon' => $shiftData = $this->resolveEntity($shiftData, $filters)];
                });

                $shiftData = $this->normalizeShiftGroups($shiftData);
                return [
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                    'data' => $shiftData
                ];
            })->values();


        $tranformedData = GvarFunc::paginate($tranformedData, 10);

        return $tranformedData;
    }
    /**
     * Get a Appointment by its id.
     */
    public function getById(int $id): Appointment
    {
        return Appointment::findOrFail($id);
    }

    /**
     * Create a
     */
    public function create(array $data)
    {
        // Check if appointment is existing, return error message
        $appointment = [];
        $isExisting = $this->isAppointmentExisting($data);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        $isPass = $this->isTimePast($data);
        if ($isPass) {
            throw new BadRequestHttpException(__('error.appointment.is_past'));
        }

        // Create an appointment and shift details
        $shiftArr = $this->splitShiftTime($data, $data['appointment_date']);
        foreach ($shiftArr as $shift) {
            $appointment[] = Appointment::create($shift);
        }
        return $appointment;
    }

    /**
     * Update an appointment
     */
    public function update(Appointment $appointment, array $data)
    {
        $newShifts = $this->splitShiftTime($data);

        // Check if appointment is past, return error message
        if ($appointment->info_disabled) {
            throw new BadRequestHttpException(__('error.appointment.disabled'));
        }

        foreach ($newShifts as $shift) {
            // Check if appointment is past, return error message
            $isPass = $this->isTimePast($data, $appointment);
            if ($isPass) {
                throw new BadRequestHttpException(__('error.appointment.is_past'));
            }
        }

        // Check if appointment is existing, return error message
        $isExisting = $this->isAppointmentExisting($data, $appointment->id);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        if (count($newShifts) == 1) {
            $appointment = $this->getById($appointment->id);
            $appointment->update($newShifts[0]);
        } else {
            foreach ($newShifts as $shift) {
                if ($shift['shift'] === $appointment->shift) {
                    $appointment = $this->getById($appointment['id']);
                    $appointment->update($shift);
                } else {
                    Appointment::create([
                        'appointment_date' => $data['appointment_date'],
                        'start_time' => $shift['start_time'],
                        'end_time' => $shift['end_time'],
                        'shift' => $shift['shift'],
                        'shelter_id' => $data['shelter_id'],
                        'team_id' => $data['team_id']
                    ]);
                }
            }
        }

        return $appointment;
    }

    /**
     * Delete an appointment
     */
    public function delete(Appointment $appointment): bool
    {
        // Check if appointment is past, return error message
        if ($appointment->info_disabled) {
            throw new BadRequestHttpException(__('error.appointment.disabled'));
        }

        return $appointment->delete();
    }

    /**
     * Check if the team has any overlapping appointments at the requested date and time.
     */
    public function isTimePast(array $data, $old = null)
    {
        $now = Carbon::now();
        $startTime = Carbon::parse($data['appointment_date'] . ' ' . $data['start_time']);
        $endTime = Carbon::parse($data['appointment_date'] . ' ' . $data['end_time']);

        if ($old) {
            $oldDate = Carbon::parse($old['appointment_date'])->format('Y-m-d');
            $newDate = Carbon::parse($data['appointment_date'])->format('Y-m-d');
            if ($startTime->isToday() && $oldDate === $newDate) {
                if ($startTime->lessThan($now)) {
                    if ($data['start_time'] !== $old['start_time']) {
                        return true;
                    }
                }

                if ($endTime->lessThan($now)) {
                    return true;
                }
            } else {
                if ($now->greaterThan($startTime) || $now->greaterThan($endTime)) {
                    return true;
                }
            }
        } else {
            if ($now->greaterThan($startTime) || $now->greaterThan($endTime)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the team has any overlapping appointments at the requested date and time.
     */
    public function isAppointmentExisting(array $data, $appointmentId = null)
    {
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];
        $allowedBorder = '13:00';

        // Find all appointment by team_id
        $appointmentQuery = Appointment::where('team_id', $data['team_id'])->where('appointment_date', $data['appointment_date']);

        // If the appointmentId exists, it means this is an update function; if not, it’s a create function.
        if ($appointmentId) {
            $appointmentQuery->where('id', '!=', $appointmentId);
        }

        // Condition for check time overlap.
        $overlap = $appointmentQuery
            // Check time is between two times.
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            // Allow this if start_time or end_time is 13:00
            ->where(function ($query) use ($startTime, $endTime, $allowedBorder) {
                $query->where(function ($sub) use ($startTime, $allowedBorder) {
                    $sub->where('end_time', '!=', $startTime)
                        ->orWhereRaw('? != ?', [$startTime, $allowedBorder]);
                })
                    ->where(function ($sub) use ($endTime, $allowedBorder) {
                        $sub->where('start_time', '!=', $endTime)
                            ->orWhereRaw('? != ?', [$endTime, $allowedBorder]);
                    });
            })->exists();
        return $overlap;
    }

    /**
     * Split the shift ("morning" or "afternoon") based on datetime.
     */
    public function splitShiftTime($appointment)
    {
        $shiftArr = [];
        $start = Carbon::parse($appointment['start_time']);
        $end   = Carbon::parse($appointment['end_time']);
        $morningStart = $start->copy()->setTime(0, 0);
        $morningEnd   = $start->copy()->setTime(13, 0);

        $eveningStart = $end->copy()->setTime(13, 0);
        $eveningEnd   = $end->copy()->setTime(23, 59, 59);

        // Check for morning shift overlap
        if (max($start, $morningStart) < min($end, $morningEnd)) {
            $shiftArr[] = [
                'appointment_date' => $appointment['appointment_date'],
                'start_time' => max($start, $morningStart)->format('H:i'),
                'end_time' => min($end, $morningEnd)->format('H:i'),
                'shift' => 1,
                'shelter_id' => $appointment['shelter_id'],
                'team_id' => $appointment['team_id']
            ];
        }

        // Check for evening shift overlap
        if (max($start, $eveningStart) < min($end, $eveningEnd)) {
            $shiftArr[] = [
                'appointment_date' => $appointment['appointment_date'],
                'start_time' => max($start, $eveningStart)->format('H:i'),
                'end_time' => min($end, $eveningEnd)->format('H:i'),
                'shift' => 2,
                'shelter_id' => $appointment['shelter_id'],
                'team_id' => $appointment['team_id']
            ];
        }

        return $shiftArr;
    }

    public function resolveEntity($data, $filters)
    {
        if (isset($filters['shelter_id']) && $filters['shelter_id']) {
            unset($data['total_shelter']);
        }

        if (isset($filters['team_id']) && $filters['team_id']) {
            unset($data['total_team']);
        }

        return $data;
    }

    public function normalizeShiftGroups($shiftGroup)
    {
        foreach (ShiftType::cases() as $shift) {
            $index = $shift->value;
            $name = Str::lower($shift->name);
            if (!$shiftGroup->has($name)) {
                $shiftGroup->put($name, (object) ['shift' => $index, 'shift_name' => $shift->label(), 'total_team' => 0, 'total_shelter' => 0, 'appointments' => []]);
            }
        }
        return $shiftGroup;
    }

    public function deleteUpcomingAppointment(array $data)
    {
        $time = Carbon::now()->format('Y-m-d H:i');
        // Delete upcoming appointment
         $appQuery = Appointment::with(['team.allUsers', 'shelter'])
            ->whereRaw("STR_TO_DATE(CONCAT(appointment_date, ' ', end_time), '%Y-%m-%d %H:%i') > ?", [$time]);

        // by team
        if (isset($data['team_id'])) {
            $appQuery->where('team_id', $data['team_id'])->delete();
        }

        // by shelter
        if (isset($data['shelter_id'])) {
            $appQuery->where('shelter_id', $data['shelter_id'])->delete();
        }
    }
}
