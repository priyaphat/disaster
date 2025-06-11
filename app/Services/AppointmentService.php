<?php

namespace App\Services;

use App\Enums\ShiftType;
use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ShiftCollection;
use App\Http\Resources\ShiftDateResource;
use App\Http\Resources\ShiftResource;
use App\Http\Utility\ConvertUtility;
use App\Http\Utility\DateUtillity;
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
    public function index(array $filters = [], ?int $limit = null)
    {
        $data = [];
        $filters['selected_month'] = Carbon::now()->format('Y-m-d');
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
        $date = isset($filters['selected_date']) ? DateUtillity::thaiToAD($filters['selected_date']) : Carbon::now()->format('Y-m-d');
        $shiftData = Appointment::filter($filters)->where(function ($query) use ($date) {
            $query->whereDate('appointment_date', $date);
        })->with('team', 'shelter')->oldest('start_time')->get();

        $data = $shiftData->groupBy('shift')->map(function ($recordsByShift, $shift) use ($filters) {
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
            return $tranformedData;
        })->values();

        return $data;
    }

    /**
     * Get month schedule.
     */
    public function getMonthSchedule(array $filters = [])
    {
        $type = isset($filters['type']) ? ConvertUtility::toInt($filters['type']) : 1;
        if ($type == 1) {
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
                    'date' => $date,
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
        $startDate = isset($filters['selected_month']) ? Carbon::parse($filters['selected_month'])->startOfMonth() : Carbon::now()->startOfMonth();
        $endDate = isset($filters['selected_month']) ? Carbon::parse($filters['selected_month'])->endOfMonth() : Carbon::now()->endOfMonth();

        $monthQuery = Appointment::with(
            'team',
            'shelter',
            'team.allUsers',
            'teamLog',
            'shelterLog',
            'userLog'
        );

        $monthAppointment = $monthQuery->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('appointment_date', [$startDate, $endDate]);
        })->get();

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
                    'date' => Carbon::parse($date)->format('d/m/Y'),
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
        // Check if appointment is existing, return error message
        $isExisting = $this->isAppointmentExisting($data, $appointment->id);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        $newShifts = $this->splitShiftTime($data);
        foreach ($newShifts as $shift) {
            if ($shift['shift'] === $appointment->shift) {
                $appointment = $this->getById($shift['id']);
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
        return $appointment->delete();
    }

    /**
     * Check if the team has any overlapping appointments at the requested date and time.
     */
    public function isAppointmentExisting(array $data, $appointmentId = null): bool
    {
        // Find all appointment by team_id
        $appointmentQuery = Appointment::where('team_id', $data['team_id']);

        // If the appointmentId exists, it means this is an update function; if not, it’s a create function.
        if ($appointmentId) {
            $appointmentQuery->where('id', '!=', $appointmentId);
        }

        // Condition for time overlap.
        return $appointmentQuery->where('appointment_date', $data['appointment_date'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
            })->exists();
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
        $morningEnd   = $start->copy()->setTime(11, 59, 59);

        $eveningStart = $end->copy()->setTime(12, 0);
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
