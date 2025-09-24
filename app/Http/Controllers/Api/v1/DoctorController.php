<?php

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Doctor\StoreRequest;
use App\Http\Requests\Api\v1\Doctor\UpdateRequest;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class DoctorController extends Controller
{
    public function index(): JsonResponse
    {
        return Response::json([
            'data' => Doctor::all(),
            'message' => 'Doctors List',
        ]);
    }

    public function show(Doctor $doctor): JsonResponse
    {
        return Response::json([
            'data' => $doctor,
            'message' => 'Doctors Show',
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $doctor = Doctor::create($request->validated());

        return Response::json([
            'data' => $doctor,
            'message' => 'Doctor Created',
        ]);
    }

    public function update(Doctor $doctor, UpdateRequest $request): JsonResponse
    {
        $doctor->update($request->validated());

        return Response::json([
            'data' => $doctor,
            'message' => 'Doctor Updated',
        ]);
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        $doctor->delete();

        return Response::json([
            'message' => 'Doctor Deleted',
        ]);
    }
}
