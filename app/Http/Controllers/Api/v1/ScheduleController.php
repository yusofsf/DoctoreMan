<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Appointment\StoreRequest;
use App\Http\Requests\Api\v1\Schedule\FindFirstAvailableTime;
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
        $schedule->update($request->validated());

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

    public function findFirstAvailableTime(User $user, FindFirstAvailableTime $request): JsonResponse
    {
        $validated = $request->validated();
        $date = $validated['date'];
        $dayOfWeek = strtolower(Carbon::parse($date)->englishDayOfWeek); // e.g. "Monday"

        $schedules = Schedule::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $bookedScheduleIds = Appointment::where('user_id', $user->id)
            ->where('date', $date)
            ->whereNot('status', AppointmentStatus::CANCELLED)
            ->whereNot('status', AppointmentStatus::AVAILABLE)
            ->pluck('schedule_id')
            ->toArray();

        foreach ($schedules as $slot) {
            if (!in_array($slot->id, $bookedScheduleIds)) {
                return Response::json([
                    'schedule_id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'date' => $date,
                    'day_of_week' => $dayOfWeek
                ]);
            }
        }

        return Response::json([
            'message' => 'No Time Available'
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
