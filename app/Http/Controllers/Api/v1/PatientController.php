<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Patient\PatientStoreRequest;
use App\Http\Requests\Api\v1\Patient\PatientUpdateRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class PatientController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator());

        return Response::json([
            'message' => 'All Patients',
            'result' => Patient::all()
        ]);
    }


    /**
     * @param Patient $patient
     * @return JsonResponse
     */
    public function show(Patient $patient): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator() || $user->isPatient());

        return Response::json([
            'message' => 'patient',
            'result' => $patient
        ]);
    }

    /**
     * @param Patient $patient
     * @return JsonResponse
     */
    public function delete(Patient $patient): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator());

        $patient->delete();

        return Response::json([
            'message' => 'patient deleted',
        ]);
    }

    /**
     * @param Patient $patient
     * @param PatientUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Patient $patient, PatientUpdateRequest $request): JsonResponse
    {
        $patient->update($request->validated());

        return Response::json([
            'message' => 'patient updated',
            'result' => $patient
        ]);
    }

    public function store(PatientStoreRequest $request): JsonResponse
    {
        $patient = Auth::user()->patient()->create($request->validated());

        return Response::json([
            'message' => 'patient stored',
            'result' => $patient
        ]);
    }
}
