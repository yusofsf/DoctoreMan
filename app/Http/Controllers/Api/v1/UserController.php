<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\User\StoreRequest;
use App\Http\Requests\Api\v1\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return Response::json([
            'data' => User::with('patient', 'doctor', 'administrator')->get(),
            'message' => 'Users List',
        ]);
    }


    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return Response::json([
            'data' => $user,
            'message' => 'User Show',
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return Response::json([
            'message' => 'User Deleted',
        ]);
    }

    /**
     * @param User $user
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(User $user, UpdateRequest $request): JsonResponse
    {
        $user->update($request->validated());

        return Response::json([
            'data' => $user,
            'message' => 'User Updated',
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return Response::json([
            'data' => $user,
            'message' => 'User Stored',
        ]);
    }
}
