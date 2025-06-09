<?php

namespace App\Services;

use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ShiftCollection;
use App\Http\Resources\ShiftDateResource;
use App\Http\Resources\ShiftResource;
use App\Models\Appointment;
use App\Models\AppointmentShift;
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
        $todayAppointment = $this->getDailySchedule($filters);

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if ($filters['type'] == 1) {
            $monthAppointment = $this->getCalendar($startDate, $endDate, $filters);
        } else {
            $monthAppointment = $this->getTimeTables($startDate, $endDate, $filters);
        }

        $data['today_schedules'] = $todayAppointment;
        $data['month_schedules'] = $monthAppointment;
        return $data;
    }

    /**
     * Get calendar detail.
     */
    public function getDailySchedule(array $filters = [])
    {
        $selectedDate = Carbon::now()->format('Y-m-d');
        if (isset($filters['selected_date'])) {
            $selectedDate = Carbon::parse($filters['selected_date'])->format('Y-m-d');
        }

        $appointmentQuery = AppointmentShift::query();
        // Orders by 'start_time' ASC
        $appointmentQuery->filter($filters)->get();
        $data = $appointmentQuery->oldest('shift_start_time')
            ->where(function ($query) use ($selectedDate) {
                $query->whereDate('shift_date', $selectedDate);
            })->get();

        $data = $data->groupBy('shift')->map(function ($recordsByShift, $shift) use ($filters) {
            $tranformedData = [
                'shift' => $shift,
                'shift_name' => $shift == 1 ? __('data.morning') : __('data.evening'),
                'total_team' => $recordsByShift->groupBy('appointment.team_id')->count(),
                'total_shelter' => $recordsByShift->groupBy('appointment.shelter_id')->count(),
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
     * Get calendar detail.
     */
    public function getCalendar($startDate, $endDate, $filters)
    {
        $monthQuery = Appointment::query();
        $monthAppointment = $monthQuery->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate]);
        })->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->start_time)->format('d-m-Y'); // returns 'YYYY-MM-DD'
            })
            ->map(function ($recordsByDate, $date) use ($filters) {
                $tranformedData = [
                    'date' => $date,
                    'total_team' => $recordsByDate->groupBy('team_id')->count(),
                    'total_shelter' => $recordsByDate->groupBy('shelter_id')->count()
            ];

            $tranformedData = $this->resetData($tranformedData, $filters);
            return $tranformedData;
            })->values();

        return $monthAppointment;
    }

    /**
     * Get time tables.
     */
    public function getTimeTables($startDate, $endDate, $filters)
    {
        $monthQuery = AppointmentShift::with(
            'appointment.team',
            'appointment.shelter',
            'appointment.team.allUsers',
            'appointment.teamLog',
            'appointment.shelterLog',
            'appointment.userLog'
        );
        $monthAppointment = $monthQuery->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('shift_date', [$startDate, $endDate]);
        })->get();

        $tranformedData = ShiftDateResource::collection($monthAppointment)->toArray(request());

        $search = isset($filters['search']) ? $filters['search'] : null;
        if ($search) {
            $tranformedData = collect($tranformedData)->filter(function ($item) use ($search) {
                return Str::contains($item['team_name'], $search);
            });
        }

        $tranformedData = collect($tranformedData)->groupBy('shift_date')
            ->map(function ($recordsByDate, $date) use ($filters) {
                $tranformedData = [
                    'date' => Carbon::parse($date)->format('d/m/Y'),
                    'data' => $recordsByDate->groupBy('shift')->map(function ($recordsByShift, $shift) {
                        return [
                            'shift' => $shift,
                            'shift_name' => $shift == 1 ? 'ช่วงเช้า' : 'ช่วงบ่าย',
                            'total_team' => $recordsByShift->groupBy('team_id')->count(),
                            'total_shelter' => $recordsByShift->groupBy('shelter_id')->count(),
                            'appointments' => ShiftResource::collection($recordsByShift)
                        ];
                    })->values(),
                ];
            $tranformedData = $this->resetData($tranformedData, $filters);
            return $tranformedData;
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
     * Create a Appointment.
     */
    public function create(array $data): Appointment
    {
        $isExisting = $this->isAppointmentExisting($data['start_time'], $data['end_time'], $data['team_id']);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        $appointment = Appointment::create($data);
        $shiftArr = $this->splitShiftTime($appointment, $data['appointment_date']);
        $appointment->shifts()->createMany($shiftArr);

        return $appointment;
    }

    /**
     * Update a Appointment.
     */
    public function update(Appointment $appointment, array $data): Appointment
    {
        $isExisting = $this->isAppointmentExisting($data['start_time'], $data['end_time'], $data['team_id'], $appointment->id);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        // Update the appointment itself
        $appointment->update($data);

        // Get new shifts from data
        $newShifts = $this->splitShiftTime($appointment, $data)->toArray();

        // Get existing shifts from DB
        $existingShifts = $appointment->shifts()->get()->toArray();

        // Index by shift type for easy comparison
        $newShiftsByType = collect($newShifts)->keyBy('shift');
        $existingShiftsByType = collect($existingShifts)->keyBy('shift');

        // Update or add shifts
        foreach ($newShiftsByType as $shiftType => $newShift) {
            if ($existingShiftsByType->has($shiftType)) {
                // Update existing shift
                $shiftModel = $appointment->shifts()->where('shift', $shiftType)->first();
                $shiftModel->update($newShift);
            } else {
                // Add new shift
                $appointment->shifts()->create($newShift);
            }
        }

        // Delete shifts that are not in new shifts
        foreach ($existingShiftsByType as $shiftType => $existingShift) {
            if (!$newShiftsByType->has($shiftType)) {
                $appointment->shifts()->where('shift', $shiftType)->delete();
            }
        }
        return $appointment;
    }

    /**
     * Delete a Appointment.
     */
    public function delete(Appointment $appointment): bool
    {
        return $appointment->delete();
    }

    /**
     * Check if the team has any overlapping appointments at the requested date and time.
     */
    public function isAppointmentExisting($startAt, $endAt, $teamId, $appointmentId = null): bool
    {
        // Find all appointment by team_id
        $appointmentQuery = Appointment::where('team_id', $teamId);

        // If the appointmentId exists, it means this is an update function; if not, it’s a create function.
        if ($appointmentId) {
            $appointmentQuery->where('id', '!=', $appointmentId);
        }

        // Condition for time overlap.
        return $appointmentQuery->where(function ($query) use ($startAt, $endAt) {
            $query->whereBetween('start_time', [$startAt, $endAt])
                ->orWhereBetween('end_time', [$startAt, $endAt]);
        })->exists();
    }


    /**
     * Split the shift ("morning" or "afternoon") based on datetime.
     */
    public function splitShiftTime($appointment, $date)
    {
        $shiftArr = [];
        $start = Carbon::parse($appointment['start_time']);
        $end   = Carbon::parse($appointment['end_time']);
        $morningStart = $start->copy()->setTime(0, 0);
        $morningEnd   = $start->copy()->setTime(11, 59, 59);

        $eveningStart = $end->copy()->setTime(12, 0);
        $eveningEnd   = $end->copy()->setTime(23, 59, 59);

        // return [$morningStart, $morningEnd, $eveningStart, $eveningEnd,  max($start, $morningStart), min($end, $morningEnd), max($start, $eveningStart), min($end, $eveningEnd)];
        // Check for morning shift overlap
        if (max($start, $morningStart) < min($end, $morningEnd)) {
            $shiftArr[] = [
                'shift_date' => $date,
                'shift_start_time' => max($start, $morningStart)->format('H:i'),
                'shift_end_time' => min($end, $morningEnd)->format('H:i'),
                'shift' => 1,
            ];
        }

        // Check for evening shift overlap
        if (max($start, $eveningStart) < min($end, $eveningEnd)) {
            $shiftArr[] = [
                'shift_date' => $date,
                'shift_start_time' => max($start, $eveningStart)->format('H:i'),
                'shift_end_time' => min($end, $eveningEnd)->format('H:i'),
                'shift' => 2,
            ];
        }

        return collect($shiftArr);
    }

    public function resetData($data, $filters)
    {
        if (isset($filters['shelter']) && $filters['shelter']) {
            unset($data['total_shelter']);
        }

        if (isset($filters['team']) && $filters['team']) {
            unset($data['total_team']);
        }

        return $data;
    }
}
