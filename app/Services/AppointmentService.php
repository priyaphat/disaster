<?php

namespace App\Services;

use App\Enums\ShiftType;
use App\Http\Controllers\API\LineController;
use App\Http\Resources\AppointmentResource;
use App\Http\Utility\ConvertUtility;
use App\Http\Utility\LogUtility;
use App\Models\Appointment;
use App\Models\Role;
use App\Utility\GvarFunc;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
     * Create an appointment
     */
    public function create(array $data)
    {
        // Check if appointment is existing, return error message
        $appointmentArr = [];
        $isExisting = $this->isAppointmentExisting($data);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        $isPast = $this->isTimePast($data);
        if ($isPast) {
            throw new BadRequestHttpException(__('error.appointment.is_past'));
        }

        // Check if the shelter is open. If it is not, the data cannot be created
        $isShelterOpen = app(ShelterService::class)->isShelterOpen($data['shelter_id']);
        if (!$isShelterOpen) {
            throw new BadRequestHttpException(__('error.shelter.closed'));
        }

        // Create an appointment and shift details
        $shiftArr = $this->splitShiftTime($data, $data['appointment_date']);
        foreach ($shiftArr as $shift) {
            $appointment = Appointment::create($shift);
            $this->notify($appointment, null, 'created');
            $appointmentArr[] = $appointment;
        }
        return $appointmentArr;
    }

    /**
     * Update an appointment
     */
    public function update(Appointment $appointment, array $data)
    {
        $oldAppointment = $appointment->replicate();
        $newShifts = $this->splitShiftTime($data);

        // Check if appointment is past, return error message
        if ($appointment->info_disabled) {
            throw new BadRequestHttpException(__('error.appointment.disabled'));
        }

        foreach ($newShifts as $shift) {
            // Check if appointment is past, return error message
            $isPast = $this->isTimePast($data, $appointment);
            if ($isPast) {
                throw new BadRequestHttpException(__('error.appointment.is_past'));
            }
        }

        // Check if appointment is existing, return error message
        $isExisting = $this->isAppointmentExisting($data, $appointment->id);
        if ($isExisting) {
            throw new BadRequestHttpException(__('error.appointment.exists'));
        }

        // Check if the shelter is open. If it is not, the data cannot be updated
        $isShelterOpen = app(ShelterService::class)->isShelterOpen($data['shelter_id']);
        if (!$isShelterOpen) {
            throw new BadRequestHttpException(__('error.shelter.closed'));
        }

        // ถ้ามีกะเดียว ให้ไปอัปเดต
        if (count($newShifts) == 1) {
            $appointment = $this->getById($appointment->id);
            $appointment->update($newShifts[0]);
            $appointment->refresh();
            $this->notify($appointment, $oldAppointment, 'updated'); // อับเดต 1 กะ ไหน ก็ได้
        } else {
            foreach ($newShifts as $shift) {
                if ($shift['shift'] == $appointment->shift->value) {
                    $appointment = $this->getById($appointment['id']);
                    $appointment->update($shift);
                    $appointment->refresh();
                    $this->notify($appointment, $oldAppointment, 'updated'); // อับเดต 2 กะ แต่มี แค่เปลี่ยน
                } else {
                    $appointment = Appointment::create([
                        'appointment_date' => $data['appointment_date'],
                        'start_time' => $shift['start_time'],
                        'end_time' => $shift['end_time'],
                        'shift' => $shift['shift'],
                        'shelter_id' => $data['shelter_id'],
                        'team_id' => $data['team_id']
                    ]);
                    $this->notify($appointment, $oldAppointment, 'created'); // อับเดต 2 กะ ไม่มีเพิ่มมา
                    // $this->notify($appointment, null, 'appointment.created'); // อับเดต 2 กะ ไม่มีเพิ่มมา
                }
            }
        }

        return $newShifts;
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

        $deleted = $appointment->delete();
        $this->notify($appointment, null, 'deleted');
        return $deleted;
    }

    /**
     * Check if the team has any overlapping appointments at the requested date and time.
     */
    public function isTimePast(array $data, $old = null)
    {
        $now = Carbon::now()->second(0)->millisecond(0);
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
        return $shiftGroup->sortBy('shift');
    }

    public function deleteUpcomingAppointment(array $data)
    {
        // by team
        if (isset($data['team_id'])) {
            LogUtility::createAppointmentLog(['team_id' => $data['team_id']]);
            Appointment::with(['team.allUsers', 'shelter'])->where('team_id', $data['team_id'])->doesntHave('teamLog')->delete();
        }

        // by shelter
        if (isset($data['shelter_id'])) {
            LogUtility::createAppointmentLog(['shelter_id' => $data['shelter_id']]);
            Appointment::with(['team.allUsers', 'shelter'])->where('shelter_id', $data['shelter_id'])->doesntHave('shelterLog')->delete();
        }
    }

    /**
     * Notification for register user
     */
    public function notify(Appointment $appointment, ?Appointment $oldAppointment = null, ?string $notifyType = null): void
    {
        $notificationService = app(NotificationService::class);

        // สร้างนัดหมาย หรือลบนัดหมาย
        if ($appointment->wasRecentlyCreated || !$oldAppointment) {
            $date = $appointment->appointment_date->thaidate('j M Y');
            $time =  Carbon::createFromFormat('H:i', $appointment->start_time)->format('G:i') . ' - ' . Carbon::createFromFormat('H:i', $appointment->end_time)->format('G:i');
            $team = $appointment->team->name;
            $shelter = $appointment->shelter->name;

            $teamNotification = $notificationService->createNotification(
                "appointment.team.{$notifyType}",
                [
                    'date' => $date,
                    'time' => $time,
                    'shelter' => $shelter
                ],
                $appointment->id,
                Appointment::class,
                true,
                $notifyType === 'deleted' ? ['shelter_id' => $appointment->shelter_id, 'team_id' => $appointment->team_id] : null
            );

            $staffNotification = $notificationService->createNotification(
                "appointment.staff.{$notifyType}",
                [
                    'date' => $date,
                    'time' => $time,
                    'team' => $team
                ],
                $appointment->id,
                Appointment::class,
                true,
                $notifyType === 'deleted' ? ['shelter_id' => $appointment->shelter_id, 'team_id' => $appointment->team_id] : null
            );

            $notificationService->sendToStaff($staffNotification, $appointment->shelter_id);
            $notificationService->sendToTeam($teamNotification, $appointment->team_id);

            $lineNotification = [
                'shelter' => $shelter,
                'date' => $date,
                'time' => $time,
                'team' => $team,
            ];
            $newStaffIds = (new LineController)->getStaffLineIds($appointment->shelter_id);
            $newTeamIds = (new LineController)->getTeamLineIds($appointment->team_id);

            if (!empty($newStaffIds)) (new LineController)->LineNotification($lineNotification, $notifyType === 'created' ? LineController::Shelter_appointment : LineController::Shelter_appointment_canceled, $newStaffIds);
            if (!empty($newTeamIds)) (new LineController)->LineNotification($lineNotification, $notifyType === 'created' ? LineController::Team_appointment : LineController::Team_appointment_canceled, $newTeamIds);
        } else {
            $shelterChanged = $appointment->shelter_id !== $oldAppointment->shelter_id;
            $teamChanged = $appointment->team_id !== $oldAppointment->team_id;
            $dateChanged = $appointment->wasChanged('start_time') || $appointment->wasChanged('end_time') || $appointment->wasChanged('appointment_date');

            $oldDate = $oldAppointment->appointment_date->thaidate('j M Y');
            $oldTime =  Carbon::createFromFormat('H:i', $oldAppointment->start_time)->format('G:i') . ' - ' . Carbon::createFromFormat('H:i', $oldAppointment->end_time)->format('G:i');
            $oldTeam = $oldAppointment->team->name;
            $oldShelter = $oldAppointment->shelter->name;

            $newDate = $appointment->appointment_date->thaidate('j M Y');
            $newTime =  Carbon::createFromFormat('H:i', $appointment->start_time)->format('G:i') . ' - ' . Carbon::createFromFormat('H:i', $appointment->end_time)->format('G:i');
            $newTeam = $appointment->team->name;
            $newTeamId = $appointment->team->id;
            $newShelter = $appointment->shelter->name;
            $newShelterId = $appointment->shelter->id;

            // dd($appointment->wasChanged('start_time'), $appointment->wasChanged('end_time'), $appointment->wasChanged('appointment_date'));
            // ถ้าเปลี่ยนทีม และเปลี่ยนศูนย์พักพิง
            if ($shelterChanged && $teamChanged) {

                // สร้างยกเลิก notification สำหรับ team
                $notifyTeamCanceled = $notificationService->createNotification(
                    'appointment.team.deleted',
                    [
                        'date' => $oldDate,
                        'time' => $oldTime,
                        'shelter' => $oldShelter
                    ],
                    $appointment->id,
                    Appointment::class,
                    true,
                    [
                        'shelter_id' => $newTeamId,
                        'team_id' => $newShelterId
                    ]
                );

                // สร้างยกเลิก notification สำหรับ staff
                $notifyStaffCanceled = $notificationService->createNotification(
                    'appointment.staff.deleted',
                    [
                        'date' => $oldDate,
                        'time' => $oldTime,
                        'team' => $oldTeam
                    ],
                    $appointment->id,
                    Appointment::class,
                    true,
                    [
                        'shelter_id' => $newTeamId,
                        'team_id' => $newShelterId
                    ]
                );

                //สร้างนัดหมาย notification สำหรับเจ้าหน้าที่ใน team
                $notifyTeamCreated = $notificationService->createNotification(
                    'appointment.team.created',
                    [
                        'date' => $newDate,
                        'time' => $newTime,
                        'shelter' => $newShelter
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );

                // สร้างนัดหมาย notification สำหรับเจ้าหน้าที่ใน shelter
                $notifyStaffCreated = $notificationService->createNotification(
                    'appointment.staff.created',
                    [
                        'date' => $newDate,
                        'time' => $newTime,
                        'team' => $newTeam
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );

                $notificationService->sendToStaff($notifyStaffCanceled, $oldAppointment->shelter_id);
                $notificationService->sendToStaff($notifyStaffCreated, $appointment->shelter_id);
                $notificationService->sendToTeam($notifyTeamCanceled, $oldAppointment->team_id);
                $notificationService->sendToTeam($notifyTeamCreated, $appointment->team_id);

                $oldStaffIds = (new LineController)->getStaffLineIds($oldAppointment->shelter_id);
                $newStaffIds = (new LineController)->getStaffLineIds($oldAppointment->shelter_id);
                $oldTeamIds = (new LineController)->getTeamLineIds($appointment->team_id);
                $newTeamIds = (new LineController)->getTeamLineIds($appointment->team_id);

                $lineCanceled = [
                    'shelter' => $oldShelter,
                    'date' => $oldDate,
                    'time' => $oldTime,
                    'team' => $oldTeam,
                ];

                $lineCreated = [
                    'shelter' => $newShelter,
                    'date' => $newDate,
                    'time' => $newTime,
                    'team' => $newTeam,
                ];

                if (!empty($oldStaffIds)) (new LineController)->LineNotification($lineCanceled, LineController::Shelter_appointment, $oldStaffIds);
                if (!empty($oldTeamIds)) (new LineController)->LineNotification($lineCanceled, LineController::Team_appointment, $oldTeamIds);
                if (!empty($newStaffIds)) (new LineController)->LineNotification($lineCreated, LineController::Shelter_appointment, $newStaffIds);
                if (!empty($newTeamIds)) (new LineController)->LineNotification($lineCreated, LineController::Team_appointment, $newTeamIds);
            }
            // ถ้าทีมไม่ถูกเปลี่ยน แต่ shelter ถูกเปลี่ยน
            else if ($shelterChanged) {
                // สร้างยกเลิก notification สำหรับเจ้าหน้าที่ใน shelter
                $notifyAppointmentCenceled = $notificationService->createNotification(
                    'appointment.staff.deleted',
                    [
                        'date' => $oldDate,
                        'time' => $oldTime,
                        'team' => $oldTeam
                    ],
                    $appointment->id,
                    Appointment::class,
                    true,
                    [
                        'shelter_id' => $newTeamId,
                        'team_id' => $newShelterId
                    ]
                );

                // สร้างนัดหมาย notification สำหรับเจ้าหน้าที่ใน shelter
                $notifyAppointmentCreated = $notificationService->createNotification(
                    'appointment.staff.created',
                    [
                        'date' => $newDate,
                        'time' => $newTime,
                        'team' => $newTeam
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );

                // สร้างอัปเดต notification สำหรับเจ้าหน้าที่ใน team
                $notifyAppointmentUpdated = $notificationService->createNotification(
                    'appointment.team.updated',
                    [
                        'old_date' => $oldDate,
                        'old_time' => $oldTime,
                        'old_shelter' => $oldShelter,
                        'new_date' => $newDate,
                        'new_time' => $newTime,
                        'new_shelter' => $newShelter
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );

                $notificationService->sendToStaff($notifyAppointmentCenceled, $oldAppointment->shelter_id);
                $notificationService->sendToStaff($notifyAppointmentCreated, $appointment->shelter_id);
                $notificationService->sendToTeam($notifyAppointmentUpdated, $appointment->team_id);

                $oldStaffIds = (new LineController)->getStaffLineIds($oldAppointment->shelter_id);
                $newStaffIds = (new LineController)->getStaffLineIds($oldAppointment->shelter_id);
                $newTeamIds = (new LineController)->getTeamLineIds($appointment->team_id);

                $lineCanceled = [
                    'shelter' => $oldShelter,
                    'date' => $oldDate,
                    'time' => $oldTime,
                    'team' => $oldTeam,
                ];

                $lineCreated = [
                    'shelter' => $newShelter,
                    'date' => $newDate,
                    'time' => $newTime,
                    'team' => $newTeam,
                ];

                $lineUpdated = [
                    'team' => $newTeam,
                    'old_date' => $oldDate,
                    'old_time' => $oldTime,
                    'old_shelter' => $oldShelter,
                    'new_date' => $newDate,
                    'new_time' => $newTime,
                    'new_shelter' => $newShelter,
                ];

                if (!empty($oldStaffIds)) (new LineController)->LineNotification($lineCanceled, LineController::Shelter_appointment_canceled, $oldStaffIds);
                if (!empty($newStaffIds)) (new LineController)->LineNotification($lineCreated, LineController::Shelter_appointment, $newStaffIds);
                if (!empty($newTeamIds)) (new LineController)->LineNotification($lineUpdated, LineController::Team_appointment_update, $newTeamIds);
            } else if ($teamChanged) {
                // สร้างยกเลิก notification
                $notifyTeamCanceled = $notificationService->createNotification(
                    'appointment.team.deleted',
                    [
                        'date' => $oldDate,
                        'time' => $oldTime,
                        'shelter' => $oldShelter
                    ],
                    $appointment->id,
                    Appointment::class,
                    true,
                    [
                        'shelter_id' => $newTeamId,
                        'team_id' => $newShelterId
                    ]
                );

                // สร้างนัดหมาย notification
                $notifyTeamCreated = $notificationService->createNotification(
                    'appointment.team.created',
                    [
                        'date' => $newDate,
                        'time' => $newTime,
                        'shelter' => $newShelter
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );

                // สร้างอัปเดต notification
                $notifyTeamUpdated = $notificationService->createNotification(
                    'appointment.staff.updated',
                    [
                        'old_date' => $oldDate,
                        'old_time' => $oldTime,
                        'old_team' => $oldTeam,
                        'new_date' => $newDate,
                        'new_time' => $newTime,
                        'new_team' => $newTeam
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );

                $notificationService->sendToTeam($notifyTeamCanceled, $oldAppointment->team_id);
                $notificationService->sendToTeam($notifyTeamCreated, $appointment->team_id);
                $notificationService->sendToStaff($notifyTeamUpdated, $appointment->shelter_id);

                $newStaffIds = (new LineController)->getStaffLineIds($oldAppointment->shelter_id);
                $oldTeamIds = (new LineController)->getTeamLineIds($oldAppointment->team_id);
                $newTeamIds = (new LineController)->getTeamLineIds($appointment->team_id);

                $lineTeamCancled = [
                    'shelter' => $oldShelter,
                    'date' => $oldDate,
                    'time' => $oldTime,
                    'team' => $oldTeam,
                ];

                $lineTeamCreated = [
                    'shelter' => $newShelter,
                    'date' => $newDate,
                    'time' => $newTime,
                    'team' => $newTeam,
                ];

                $lineShelterUpdated = [
                    'shelter' => $oldShelter,
                    'old_date' => $oldDate,
                    'old_time' => $oldTime,
                    'old_team' => $oldTeam,
                    'new_date' => $newDate,
                    'new_time' => $newTime,
                    'new_team' => $newTeam,
                ];

                if (!empty($oldTeamIds)) (new LineController)->LineNotification($lineTeamCancled, LineController::Team_appointment_canceled, $oldTeamIds);
                if (!empty($newTeamIds)) (new LineController)->LineNotification($lineTeamCreated, LineController::Team_appointment, $newTeamIds);
                if (!empty($newStaffIds)) (new LineController)->LineNotification($lineShelterUpdated, LineController::Shelter_appointment_update, $newStaffIds);
            } else if ($dateChanged) {
                // $check = $appointment->wasRecentlyCreated || $dateChanged;
                $notifyTeam = $notificationService->createNotification(
                    'appointment.team.updated',
                    [
                        'old_date' => $oldDate,
                        'old_time' => $oldTime,
                        'old_shelter' => $oldShelter,
                        'new_date' => $newDate,
                        'new_time' => $newTime,
                        'new_shelter' => $newShelter
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );

                $notifyStaff = $notificationService->createNotification(
                    'appointment.staff.updated',
                    [
                        'old_date' => $oldDate,
                        'old_time' => $oldTime,
                        'old_team' => $oldTeam,
                        'new_date' => $newDate,
                        'new_time' => $newTime,
                        'new_team' => $newTeam
                    ],
                    $appointment->id,
                    Appointment::class,
                    true
                );
                $notificationService->sendToStaff($notifyStaff, $appointment->shelter_id);
                $notificationService->sendToTeam($notifyTeam, $appointment->team_id);

                $newStaffIds = (new LineController)->getStaffLineIds($oldAppointment->shelter_id);
                $newTeamIds = (new LineController)->getTeamLineIds($appointment->team_id);

                $lineTeamUpdated = [
                    'team' => $newTeam,
                    'old_date' => $oldDate,
                    'old_time' => $oldTime,
                    'old_shelter' => $oldShelter,
                    'new_date' => $newDate,
                    'new_time' => $newTime,
                    'new_shelter' => $newShelter
                ];

                $lineShelterUpdated = [
                    'shelter' => $oldShelter,
                    'old_date' => $oldDate,
                    'old_time' => $oldTime,
                    'old_team' => $oldTeam,
                    'new_date' => $newDate,
                    'new_time' => $newTime,
                    'new_team' => $newTeam,
                ];

                if (!empty($newStaffIds)) (new LineController)->LineNotification($lineShelterUpdated, LineController::Shelter_appointment_update, $newStaffIds);
                if (!empty($newTeamIds)) (new LineController)->LineNotification($lineTeamUpdated, LineController::Team_appointment_update, $newTeamIds);
            } else {
                // error_log('ไม่เปลี่ยนอะไรเลย');
            }
        }
    }
}
