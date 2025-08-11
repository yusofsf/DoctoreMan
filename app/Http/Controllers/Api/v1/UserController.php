<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Doctor\DoctorStoreRequest;
use App\Http\Requests\Api\v1\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Jobs\GenerateSchedules;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator());

        return Response::json([
            'message' => 'All Users',
            'result' => User::with('patient', 'doctor')->get()
        ]);
    }

    public function indexDoctor(): JsonResponse
    {
        return Response::json([
            'message' => 'All Doctors',
            'result' => User::where('role', UserRole::DOCTOR)->get()
        ]);
    }


    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator());

        return Response::json([
            'message' => 'user',
            'result' => $user
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function delete(User $user): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->isAdministrator());

        $user->delete();

        return Response::json([
            'message' => 'user deleted',
        ]);
    }

    /**
     * @param User $user
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function update(User $user, UserUpdateRequest $request): JsonResponse
    {
        $user->update($request->validated());

        return Response::json([
            'message' => 'user updated',
            'result' => $user
        ]);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if ($validated['role'] === UserRole::DOCTOR) {
            $doctor = User::create($validated);

            // Dispatch job asynchronously without waiting
            GenerateSchedules::dispatch($doctor)->onQueue('schedules');

            return Response::json([
                'message' => 'doctor stored',
                'result' => $doctor
            ]);
        }

        $admin = User::create($validated);

        return Response::json([
            'message' => 'admin stored',
            'result' => $admin
        ]);
    }
}
