<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\QueueController as AdminQueueController;
use App\Http\Controllers\Doctor\ScheduleController as DoctorScheduleController;
use App\Http\Controllers\Doctor\QueueController as DoctorQueueController;
use App\Http\Controllers\Patient\QueueController as PatientQueueController;
use App\Http\Controllers\Auth\PatientRegisterController;
use App\Http\Controllers\QueueDisplayController;

// ── ROOT ──
Route::get('/', function () {
    return redirect()->route('login');
});

// ── REGISTER PASIEN (PUBLIC) ──
Route::get('/register/pasien', [PatientRegisterController::class, 'create'])
    ->name('patient.register')
    ->middleware('guest');

Route::post('/register/pasien', [PatientRegisterController::class, 'store'])
    ->name('patient.register.store')
    ->middleware('guest');

// ── DISPLAY ANTRIAN LIVE (PUBLIC - untuk TV/Monitor) ──
Route::get('/display', [QueueDisplayController::class, 'show'])
    ->name('queue.display');

Route::get('/display/{clinic}', [QueueDisplayController::class, 'show'])
    ->name('queue.display.clinic');

// ── AUTHENTICATED ROUTES ──
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ══ ADMIN ══
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn() => redirect()->route('dashboard'))->name('dashboard');

        Route::resource('clinics', ClinicController::class);
        Route::patch('clinics/{clinic}/toggle', [ClinicController::class, 'toggleStatus'])
            ->name('clinics.toggle');

        Route::resource('doctors', DoctorController::class)->except(['show']);

        Route::resource('schedules', AdminScheduleController::class)->except(['show']);

        Route::get('/queues', [AdminQueueController::class, 'index'])
            ->name('queues.index');
        Route::patch('/queues/{queue}/cancel', [AdminQueueController::class, 'cancel'])
            ->name('queues.cancel');
    });

    // ══ DOCTOR ══
    Route::middleware(['role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', fn() => redirect()->route('dashboard'))->name('dashboard');

        Route::get('/schedules', [DoctorScheduleController::class, 'index'])
            ->name('schedules.index');

        Route::get('/queues', [DoctorQueueController::class, 'index'])
            ->name('queues.index');
        Route::patch('/queues/{queue}/call', [DoctorQueueController::class, 'call'])
            ->name('queues.call');
        Route::patch('/queues/{queue}/start', [DoctorQueueController::class, 'start'])
            ->name('queues.start');
        Route::patch('/queues/{queue}/finish', [DoctorQueueController::class, 'finish'])
            ->name('queues.finish');
            // Di dalam group doctor:
        Route::patch('/attendance/toggle', [\App\Http\Controllers\Doctor\AttendanceController::class, 'toggle'])
    ->name('attendance.toggle');
    });

    // ══ PATIENT ══
    Route::middleware(['role:patient'])->prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', fn() => redirect()->route('dashboard'))->name('dashboard');

        Route::get('/queues', [PatientQueueController::class, 'index'])
            ->name('queues.index');
        Route::get('/queues/create', [PatientQueueController::class, 'create'])
            ->name('queues.create');
        Route::post('/queues', [PatientQueueController::class, 'store'])
            ->name('queues.store');
        Route::patch('/queues/{queue}/cancel', [PatientQueueController::class, 'cancel'])
            ->name('queues.cancel');
    });
});

require __DIR__.'/auth.php';