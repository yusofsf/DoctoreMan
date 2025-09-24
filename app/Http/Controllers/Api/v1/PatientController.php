<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Patient\StoreRequest;
use App\Http\Requests\Api\v1\Patient\UpdateRequest;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PatientController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return Response::json([
            'data' => Patient::all(),
            'message' => 'Patients List',

        ]);
    }


    /**
     * @param Patient $patient
     * @return JsonResponse
     */
    public function show(Patient $patient): JsonResponse
    {
        return Response::json([
            'data' => $patient,
            'message' => 'Patient Show',
        ]);
    }

    /**
     * @param Patient $patient
     * @return JsonResponse
     */
    public function destroy(Patient $patient): JsonResponse
    {
        $patient->delete();

        return Response::json([
            'message' => 'Patient Deleted',
        ]);
    }

    /**
     * @param Patient $patient
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Patient $patient, UpdateRequest $request): JsonResponse
    {
        $patient->update($request->validated());

        return Response::json([
            'data' => $patient,
            'message' => 'Patient Updated',
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $patient = Auth::user()->patient()->create($request->validated());

        return Response::json([
            'data' => $patient,
            'message' => 'Patient Stored',
        ]);
    }
}
