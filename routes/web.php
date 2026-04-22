<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueDisplayController;
use App\Http\Controllers\Admin\HospitalController as AdminHospitalController;
use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\QueueController as AdminQueueController;
use App\Http\Controllers\Doctor\ScheduleController as DoctorScheduleController;
use App\Http\Controllers\Doctor\QueueController as DoctorQueueController;
use App\Http\Controllers\Doctor\AttendanceController;
use App\Http\Controllers\Patient\QueueController as PatientQueueController;
use App\Http\Controllers\Auth\PatientRegisterController;

Route::get('/', fn() => redirect()->route('login'));

// Register pasien
Route::middleware('guest')->group(function () {
    Route::get('/daftar', [PatientRegisterController::class, 'create'])->name('patient.register');
    Route::post('/daftar', [PatientRegisterController::class, 'store'])->name('patient.register.store');
});

// Display TV (public)
Route::get('/display', [QueueDisplayController::class, 'index'])->name('queue.display');
Route::get('/display/{hospital}', [QueueDisplayController::class, 'show'])->name('queue.display.hospital');

// Authenticated
Route::middleware(['auth','verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // ══ SUPER ADMIN ══
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn() => redirect()->route('dashboard'))->name('dashboard');

        // Hospital CRUD
        Route::resource('hospitals', AdminHospitalController::class);

        // Scoped by hospital
        Route::prefix('{hospital}')->group(function () {
            Route::resource('clinics', ClinicController::class)->except(['show']);
            Route::patch('clinics/{clinic}/toggle', [ClinicController::class, 'toggleStatus'])->name('clinics.toggle');
            Route::resource('doctors', DoctorController::class)->except(['show']);
            Route::resource('schedules', AdminScheduleController::class)->except(['show']);
            Route::get('queues', [AdminQueueController::class, 'index'])->name('queues.index');
            Route::patch('queues/{queue}/cancel', [AdminQueueController::class, 'cancel'])->name('queues.cancel');
        });
    });

    // ══ DOCTOR ══
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', fn() => redirect()->route('dashboard'))->name('dashboard');
        Route::get('/queues', [DoctorQueueController::class, 'index'])->name('queues.index');
        Route::patch('/queues/{queue}/call', [DoctorQueueController::class, 'call'])->name('queues.call');
        Route::patch('/queues/{queue}/start', [DoctorQueueController::class, 'start'])->name('queues.start');
        Route::patch('/queues/{queue}/finish', [DoctorQueueController::class, 'finish'])->name('queues.finish');
        Route::get('/schedules', [DoctorScheduleController::class, 'index'])->name('schedules.index');
        Route::patch('/attendance', [AttendanceController::class, 'toggle'])->name('attendance.toggle');
    });

    // ══ PATIENT ══
    Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', fn() => redirect()->route('dashboard'))->name('dashboard');
        Route::get('/queues', [PatientQueueController::class, 'index'])->name('queues.index');
        Route::get('/booking', [PatientQueueController::class, 'create'])->name('queues.create');
        Route::post('/booking', [PatientQueueController::class, 'store'])->name('queues.store');
        Route::patch('/queues/{queue}/cancel', [PatientQueueController::class, 'cancel'])->name('queues.cancel');

        // AJAX endpoints untuk booking flow
        Route::get('/booking/hospitals', [PatientQueueController::class, 'getHospitals'])->name('booking.hospitals');
        Route::get('/booking/clinics', [PatientQueueController::class, 'getClinics'])->name('booking.clinics');
        Route::get('/booking/schedules', [PatientQueueController::class, 'getSchedules'])->name('booking.schedules');
    });
});

require __DIR__.'/auth.php';