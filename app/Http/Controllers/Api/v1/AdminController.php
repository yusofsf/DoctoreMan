<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\StoreRequest;
use App\Http\Requests\Api\v1\Admin\UpdateRequest;
use App\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    use AuthorizesRequests;
    public function index(): JsonResponse
    {
        return Response::json([
            'data' => Admin::all(),
            'message' => 'Admins List'
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $admin = Admin::create($request->validated());

        return Response::json([
            'data' => $admin,
            'message' => 'Admin Created'
        ]);
    }

    public function show(Admin $admin): JsonResponse
    {
        return Response::json([
            'data' => $admin,
            'message' => 'Admin Show',
        ]);
    }

    public function update(Admin $admin, UpdateRequest $request): JsonResponse
    {
        $admin->update($request->validated());

        return Response::json([
            'data' => $admin,
            'message' => 'Admin Updated'
        ]);
    }

    public function destroy(Admin $admin): JsonResponse
    {
        $admin->delete();

        return Response::json([
            'message' => 'Admin Deleted'
        ]);
    }
}
