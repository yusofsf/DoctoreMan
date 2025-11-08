<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Appointment\StoreRequest;
use App\Http\Requests\Api\v1\Schedule\UpdateRequest;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ScheduleController extends Controller
{
    /**
     * @param Schedule $schedule
     * @return JsonResponse
     */
    public function show(Schedule $schedule): JsonResponse
    {
        return Response::json([
            'data' => $schedule,
            'message' => 'Schedule Show',
        ]);
    }

    /**
     * @param Schedule $schedule
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Schedule $schedule, UpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Auto-calculate end_time if not provided
        if ((empty($data['end_time']) || !$data['end_time']) && !empty($data['start_time']) && !empty($schedule->user_id)) {
            $user = User::find($schedule->user_id);
            $consultationMinutes = $user?->doctor?->consultation_duration ?? 30;

            try {
                $start = Carbon::parse($data['start_time']);
                $data['end_time'] = $start->copy()->addMinutes((int) $consultationMinutes)->format('H:i:s');
            } catch (\Throwable $e) {
            }
        }
        
        // Validate that start_time and end_time are within doctor's working hours
        $dayOfWeek = $data['day_of_week'] ?? $schedule->day_of_week;
        $user = User::find($schedule->user_id);
        
        if ($user && $user->doctor) {
            $doctor = $user->doctor;
            
            // Get working hours for this day
            $workingHours = $doctor->getWorkingHoursForDay($dayOfWeek);
            
            // Check if this is a working day
            if (!$workingHours['is_working']) {
                return Response::json([
                    'message' => 'Doctor does not work on this day',
                    'errors' => ['day_of_week' => 'Doctor does not work on this day']
                ], 422);
            }
            
            // Validate time within working hours if times are set
            if (!empty($workingHours['start_time']) && !empty($workingHours['end_time'])) {
                try {
                    $scheduleStart = Carbon::parse($data['start_time']);
                    $scheduleEnd = Carbon::parse($data['end_time']);
                    $workStart = Carbon::parse($workingHours['start_time']);
                    $workEnd = Carbon::parse($workingHours['end_time']);
                    
                    // Check if schedule is within working hours
                    if ($scheduleStart->lt($workStart) || $scheduleEnd->gt($workEnd)) {
                        return Response::json([
                            'message' => 'Schedule time must be within doctor working hours (' . $workStart->format('H:i') . ' - ' . $workEnd->format('H:i') . ')',
                            'errors' => ['start_time' => 'Schedule time must be within doctor working hours']
                        ], 422);
                    }
                } catch (\Throwable $e) {
                    // Silent catch for parsing errors
                }
            }
        }
        
        $schedule->update($data);

        return Response::json([
            'data' => $schedule,
            'message' => 'Schedule Updated',
        ]);
    }

    public function reserve(User $user, Schedule $schedule, StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $existingAppointment = Appointment::where('schedule_id', $schedule->id)
            ->where('date', $validated['date'])
            ->whereNot('status', AppointmentStatus::AVAILABLE)
            ->whereNot('status', AppointmentStatus::CANCELLED)
            ->first();

        if ($existingAppointment) {
            return Response::json([
                'message' => 'appointment already reserved',
            ], 400);
        }

        $appointment = Auth::user()->appointments()->create([
            'date' => $validated['date'],
            'status' => AppointmentStatus::RESERVED,
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'patient_id' => Auth::user()->id,
        ]);

        $schedule->update([
            'patient_id' => Auth::user()->id
        ]);

        return Response::json([
            'data' => $appointment,
            'message' => 'AppointmentPolicy Stored',
        ]);
    }

    public function allSchedules(User $user): JsonResponse
    {
        if ($user->isDoctor()) {
            return Response::json([
                'data' => $user->doctorSchedules()->get(),
                'message' => "All $user->first_name $user->last_name Schedules"
            ]);
        }

        return Response::json([
            'data' => Schedule::all(),
            'message' => "Schedules List"
        ]);
    }
}
