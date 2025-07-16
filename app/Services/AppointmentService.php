<?php

namespace App\Services;

use App\Enums\ShiftType;
use App\Http\Resources\AppointmentResource;
use App\Http\Utility\ConvertUtility;
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

        $shiftData = Appointment::filter($filters)->with('team', 'shelter')->oldest('start_time')->get();

        $data = $shiftData->groupBy('shift')->mapWithKeys(function ($recordsByShift, $shift) use ($filters) {
            $tranformedData = [
                'shift' => $shift,
                'shift_name' => ShiftType::tryFrom($shift)->label(),
                'total_team' => $recordsByShift->groupBy('team_id')->count(),
                'total_shelter' => $recordsByShift->groupBy('shelter_id')->count(),
                'appointments' => $recordsByShift->map(function ($record) {
                    return new AppointmentResource($record);
                })->values(),
            ];

            $tranformedData = $this->resetData($tranformedData, $filters);
            return [ShiftType::tryFrom($shift)->type() => $tranformedData];
        });

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
                $tranformedData = [
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                    'total_team' => $recordsByDate->groupBy('team_id')->count(),
                    'total_shelter' => $recordsByDate->groupBy('shelter_id')->count()
                ];
                $tranformedData = $this->resetData($tranformedData, $filters);
                return $tranformedData;
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
            'userLog'
        )->filter($filters)->oldest('appointment_date', 'start_time')->get();


        $tranformedData = AppointmentResource::collection($monthAppointment)->toArray(request());

        $search = isset($filters['search']) ? $filters['search'] : null;
        if ($search) {
            $tranformedData = collect($tranformedData)->filter(function ($item) use ($search) {
                return Str::contains($item['team_name'], $search);
            });
        }

        $tranformedData = collect($tranformedData)->groupBy('appointment_date')
            ->map(function ($recordsByDate, $date) use ($filters) {
                return [
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                    'data' => $recordsByDate->groupBy('shift')->map(function ($recordsByShift, $shift) use ($filters) {
                        $tranformedData = [
                            'shift' => $shift,
                            'shift_name' => ShiftType::tryFrom($shift)->label(),
                            'total_team' => $recordsByShift->groupBy('team_id')->count(),
                            'total_shelter' => $recordsByShift->groupBy('shelter_id')->count(),
                            'appointments' => $recordsByShift
                        ];
                        $tranformedData = $this->resetData($tranformedData, $filters);
                        return $tranformedData;
                    })->values(),
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
    public function update(Appointment $appointment, array $data): Appointment
    {
        // Check if appointment is past, return error message
        if ($appointment->info_disabled) {
            throw new BadRequestHttpException(__('error.appointment.is_past'));
        }
        // Check if appointment is existing, return error message
        $isExisting = $this->isAppointmentExisting($data, $appointment->id);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        $newShifts = $this->splitShiftTime($data);
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

        return $appointment;
    }

    /**
     * Delete an appointment
     */
    public function delete(Appointment $appointment): bool
    {
        // Check if appointment is past, return error message
        if ($appointment->info_disabled) {
            throw new BadRequestHttpException(__('error.appointment.is_past'));
        }

        return $appointment->delete();
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

        $afternoonStart = $end->copy()->setTime(13, 0);
        $afternoonEnd   = $end->copy()->setTime(23, 59, 59);

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
        if (max($start, $afternoonStart) < min($end, $afternoonEnd)) {
            $shiftArr[] = [
                'appointment_date' => $appointment['appointment_date'],
                'start_time' => max($start, $afternoonStart)->format('H:i'),
                'end_time' => min($end, $afternoonEnd)->format('H:i'),
                'shift' => 2,
                'shelter_id' => $appointment['shelter_id'],
                'team_id' => $appointment['team_id']
            ];
        }

        return $shiftArr;
    }

    public function resetData($data, $filters)
    {
        if (isset($filters['shelter_id']) && $filters['shelter_id']) {
            unset($data['total_shelter']);
        }

        if (isset($filters['team_id']) && $filters['team_id']) {
            unset($data['total_team']);
        }

        return $data;
    }
}
