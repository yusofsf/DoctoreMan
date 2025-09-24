<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Appointment\UpdateRequest;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if (Auth::user()->isDoctor()) {
            $user = Auth::user();

            return Response::json([
                'data' => $user->doctor->appointments()
                    ->where('status', AppointmentStatus::RESERVED)
                    ->orWhere('status', AppointmentStatus::APPROVED)
                    ->get(),
                'message' => "All Appointments for $user->first_name $user->last_name",
            ]);
        }

        return Response::json([
            'data' => Appointment::whereIn('status', [AppointmentStatus::RESERVED, AppointmentStatus::APPROVED])->get(),
            'message' => 'All Appointments Reserved or Approved',

        ]);
    }


    /**
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function show(Appointment $appointment): JsonResponse
    {
        return Response::json([
            'data' => $appointment,
            'message' => 'AppointmentPolicy Show',
        ]);
    }

    /**
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function cancel(Appointment $appointment): JsonResponse
    {
        $appointment->update([
            'status' => AppointmentStatus::CANCELLED
        ]);

        return Response::json([
            'message' => 'AppointmentPolicy Cancelled',
        ]);
    }

    /**
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function approve(Appointment $appointment): JsonResponse
    {
        $appointment->update([
            'status' => AppointmentStatus::APPROVED
        ]);

        return Response::json([
            'message' => 'AppointmentPolicy Approved',
        ]);
    }

    /**
     * @param Appointment $appointment
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Appointment $appointment, UpdateRequest $request): JsonResponse
    {
        $appointment->update($request->validated());

        return Response::json([
            'data' => $appointment,
            'message' => 'AppointmentPolicy Updated',
        ]);
    }
}
