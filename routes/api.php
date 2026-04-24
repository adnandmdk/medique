<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\DashboardController;

// =============================================
// PUBLIC ROUTES (tidak perlu login)
// =============================================
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Info poliklinik & jadwal bisa dilihat publik
Route::get('/clinics',             [ClinicController::class, 'index']);
Route::get('/clinics/{clinic}',    [ClinicController::class, 'show']);
Route::get('/schedules',           [ScheduleController::class, 'index']);
Route::get('/schedules/{schedule}',[ScheduleController::class, 'show']);

Route::get('/', function () {
    return response()->json(['message' => 'API OK']);
});

// =============================================
// AUTHENTICATED ROUTES
// =============================================
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard (role-based)
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // =============================================
    // PATIENT ROUTES
    // =============================================
    Route::middleware('role:patient')->prefix('patient')->group(function () {
        Route::get('/queues',                   [QueueController::class, 'index']);
        Route::post('/queues',                  [QueueController::class, 'store']);
        Route::get('/queues/{queue}',           [QueueController::class, 'show']);
        Route::patch('/queues/{queue}/cancel',  [QueueController::class, 'cancel']);
    });

    // =============================================
    // DOCTOR ROUTES
    // =============================================
    Route::middleware('role:doctor')->prefix('doctor')->group(function () {
        Route::get('/queues',                         [QueueController::class, 'doctorQueue']);
        Route::patch('/queues/{queue}/call',          [QueueController::class, 'call']);
        Route::patch('/queues/{queue}/start',         [QueueController::class, 'start']);
        Route::patch('/queues/{queue}/finish',        [QueueController::class, 'finish']);
    });

});