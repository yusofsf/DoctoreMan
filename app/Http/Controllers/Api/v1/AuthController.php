<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();

            $token = $user->createToken($user->user_name . $user->password . Carbon::now())->plainTextToken;

            return Response::json([
                'data' => $user,
                'token' => $token,
                'message' => 'User Successfully Logged In',
            ]);
        }

        return Response::json([
            'message' => 'User Not Found'
        ], 404);
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $token = $user->createToken($user->user_name . $user->password . Carbon::now())->plainTextToken;

        return Response::json([
            'data' => $user,
            'token' => $token,
            'message' => 'User Created'
        ], 201);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return Response::json([
            'message' => 'User Logged Out',
        ], 202);
    }
}
