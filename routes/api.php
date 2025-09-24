<?php

use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\AppointmentController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\DoctorController;
use App\Http\Controllers\Api\v1\PatientController;
use App\Http\Controllers\Api\v1\ScheduleController;
use App\Http\Controllers\Api\v1\SearchController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group( function () {
    Route::prefix('/auth')->middleware('throttle:10,1')
        ->controller(AuthController::class)->group(function () {
            Route::post('/login', 'login');
            Route::post('/register', 'register');
            Route::get('/logout', 'logout')->middleware('auth:sanctum');
        });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('users')
            ->controller(UserController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{user}', 'show');
                Route::delete('/{user}', 'destroy');
                Route::patch('/{user}', 'update');
            });

        Route::prefix('doctors')
            ->controller(DoctorController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{doctor}', 'show');
                Route::delete('/{doctor}', 'destroy');
                Route::patch('/{doctor}', 'update');
            });

        Route::prefix('admins')
            ->controller(AdminController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{admin}', 'show');
                Route::delete('/{admin}', 'destroy');
                Route::patch('/{admin}', 'update');
            });

        Route::prefix('patients')
            ->controller(PatientController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('', 'store');
                Route::get('/{patient}', 'show');
                Route::delete('/{patient}', 'destroy');
                Route::patch('/{patient}', 'update');
            });

        Route::prefix('schedules')
            ->controller(ScheduleController::class)->group(function () {
                Route::get('/{schedule}', 'show');
                Route::patch('/{schedules}', 'update');
                Route::post('/{schedule}/users/{user}/reserve', 'reserve');
                Route::get('/users/{user}/schedules', 'allSchedules');
            });

        Route::prefix('appointments')
            ->controller(AppointmentController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{appointment}', 'show');
                Route::get('/{appointment}/cancel', 'cancel');
                Route::get('/{appointment}/approve', 'approve');
                Route::patch('/{appointment}', 'update');
            });
    });

    Route::get('/search', SearchController::class);
});