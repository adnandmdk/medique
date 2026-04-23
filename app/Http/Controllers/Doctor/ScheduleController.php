<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user()->doctor;

        $schedules = $doctor
            ? Schedule::with(['doctor.clinic'])
                ->where('doctor_id', $doctor->id)
                ->orderByRaw("FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
                ->get()
            : collect();

        return view('doctor.schedules.index', compact('schedules', 'doctor'));
    }
}