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
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

// ── ROOT ──
Route::get('/', fn() => redirect()->route('login'));

Route::get('/debug-db', function () {
    return response()->json([
        'host' => env('DB_HOST'),
        'database' => env('DB_DATABASE'),
        'user' => env('DB_USERNAME'),
    ]);
});
Route::get('/migrate-now', function () {
    Artisan::call('migrate --force');
    return 'Migrated!';
    });
    Route::get('/create-admin', function () {
    User::create([
        'name' => 'Admin',
        'email' => 'admin@medique.test',
        'password' => Hash::make('password123'),
    ]);
    return 'Admin created!';
});

// ── REGISTER PASIEN (guest only) ──
Route::middleware('guest')->group(function () {
    Route::get('/daftar', [PatientRegisterController::class, 'create'])
        ->name('patient.register');
    Route::post('/daftar', [PatientRegisterController::class, 'store'])
        ->name('patient.register.store');
});

// ── DISPLAY TV (public) ──
Route::get('/display', [QueueDisplayController::class, 'index'])
    ->name('queue.display');
Route::get('/display/{clinic}', [QueueDisplayController::class, 'show'])
    ->name('queue.display.clinic');

    Route::patch('/admin/queues/{queue}/cancel', [AdminQueueController::class, 'cancel'])
    ->name('admin.queues.cancel');
// ── AUTHENTICATED ──
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'show'])
        ->name('profile.show');
    Route::put('/profil', [ProfileController::class, 'update'])
        ->name('profile.update');

    // ══ ADMIN ══
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        Route::get('/dashboard', fn() => redirect()->route('dashboard'))
            ->name('dashboard');

        // Hospital CRUD
        Route::resource('hospitals', AdminHospitalController::class);

        // Clinic (scoped by hospital)
        Route::resource('hospitals.clinics', ClinicController::class)
            ->except(['show']);
        Route::patch('hospitals/{hospital}/clinics/{clinic}/toggle',
            [ClinicController::class, 'toggleStatus'])
            ->name('hospitals.clinics.toggle');

        // Doctor
        Route::resource('hospitals.doctors', DoctorController::class)
            ->except(['show']);

        // Schedule
        Route::resource('hospitals.schedules', AdminScheduleController::class)
            ->except(['show']);

        // Queue
        Route::get('hospitals/{hospital}/queues',
            [AdminQueueController::class, 'index'])
            ->name('hospitals.queues.index');
        Route::patch('hospitals/{hospital}/queues/{queue}/cancel',
            [AdminQueueController::class, 'cancel'])
            ->name('hospitals.queues.cancel');
    });

    // ══ DOCTOR ══
    Route::middleware('role:doctor')
        ->prefix('doctor')
        ->name('doctor.')
        ->group(function () {

        Route::get('/dashboard', fn() => redirect()->route('dashboard'))
            ->name('dashboard');
        Route::get('/queues', [DoctorQueueController::class, 'index'])
            ->name('queues.index');
        Route::patch('/queues/{queue}/call', [DoctorQueueController::class, 'call'])
            ->name('queues.call');
        Route::patch('/queues/{queue}/start', [DoctorQueueController::class, 'start'])
            ->name('queues.start');
        Route::patch('/queues/{queue}/finish', [DoctorQueueController::class, 'finish'])
            ->name('queues.finish');
        Route::get('/schedules', [DoctorScheduleController::class, 'index'])
            ->name('schedules.index');
        Route::patch('/attendance', [AttendanceController::class, 'toggle'])
            ->name('attendance.toggle');
            Route::get('/doctor/queues', [DoctorQueueController::class, 'index'])
    ->name('doctor.queues.index');
    });

    // ══ PATIENT ══
    Route::middleware('role:patient')
        ->prefix('patient')
        ->name('patient.')
        ->group(function () {

        Route::get('/dashboard', fn() => redirect()->route('dashboard'))
            ->name('dashboard');
        Route::get('/queues', [PatientQueueController::class, 'index'])
            ->name('queues.index');
        Route::get('/booking', [PatientQueueController::class, 'create'])
            ->name('queues.create');
        Route::post('/booking', [PatientQueueController::class, 'store'])
            ->name('queues.store');
        Route::patch('/queues/{queue}/cancel', [PatientQueueController::class, 'cancel'])
            ->name('queues.cancel');

        // AJAX endpoints booking
        Route::get('/booking/hospitals', [PatientQueueController::class, 'getHospitals'])
            ->name('booking.hospitals');
        Route::get('/booking/clinics', [PatientQueueController::class, 'getClinics'])
            ->name('booking.clinics');
        Route::get('/booking/schedules', [PatientQueueController::class, 'getSchedules'])
            ->name('booking.schedules');
    });
    
});
Route::get('/setup-roles', function () {

    // buat role kalau belum ada
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
    $patientRole = Role::firstOrCreate(['name' => 'patient']);

    // assign ke user tertentu (contoh)
    $admin = User::where('email', 'admin@medique.test')->first();
    if ($admin) $admin->assignRole($adminRole);

    $doctor = User::where('email', 'doctor@medique.test')->first();
    if ($doctor) $doctor->assignRole($doctorRole);

    $patient = User::where('email', 'patient@medique.test')->first();
    if ($patient) $patient->assignRole($patientRole);

    return 'Roles ready!';
});
require __DIR__.'/auth.php';