<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Appointment\AppointmentStoreRequest;
use App\Http\Requests\Api\v1\Appointment\AppointmentUpdateRequest;
use App\Http\Requests\Api\v1\Schedule\ScheduleStoreRequest;
use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentSet;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator() || $user->isDoctor());

        if (Auth::user()->isDoctor()) {
            $user = Auth::user();

            return Response::json([
                'message' => "All Appointments for $user->first_name $user->last_name",
                'result' => $user->doctor->appointments()
                    ->where('status', AppointmentStatus::RESERVED)
                    ->orWhere('status', AppointmentStatus::APPROVED)
                    ->get()
            ]);
        }

        return Response::json([
            'message' => 'All Appointments Reserved or Approved',
            'result' => Appointment::whereIn('status', [AppointmentStatus::RESERVED, AppointmentStatus::APPROVED])->get()
        ]);
    }


    /**
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function show(Appointment $appointment): JsonResponse
    {
        return Response::json([
            'message' => 'appointment',
            'result' => $appointment
        ]);
    }

    /**
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function cancel(Appointment $appointment): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator() || $user->isDoctor());

        $appointment->update([
            'status' => AppointmentStatus::CANCELLED
        ]);
        
        if ($appointment->patient?->user) {
            Mail::to($appointment->patient->user)->send(new AppointmentCancelled(
                $appointment,
                $appointment->schedule,
                $appointment->doctor,
                $appointment->patient->user,
            ));
        }

        return Response::json([
            'message' => 'appointment cancelled',
        ]);
    }

    /**
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function approve(Appointment $appointment): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isDoctor());

        $appointment->update([
            'status' => AppointmentStatus::APPROVED
        ]);

        return Response::json([
            'message' => 'appointment approved',
        ]);
    }

    /**
     * @param Appointment $appointment
     * @param AppointmentUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Appointment $appointment, AppointmentUpdateRequest $request): JsonResponse
    {
        $appointment->update($request->validated());

        return Response::json([
            'message' => 'appointment updated',
            'result' => $appointment
        ]);
    }
}
