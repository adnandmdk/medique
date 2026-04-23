<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Hospital $hospital): View
    {
        $schedules = Schedule::with(['doctor.user', 'doctor.clinic'])
            ->whereHas('doctor', fn($q) =>
                $q->where('hospital_id', $hospital->id)
            )
            ->paginate(15);

        return view('admin.schedules.index', compact('hospital', 'schedules'));
    }

    public function create(Hospital $hospital): View
    {
        $doctors = Doctor::with(['user', 'clinic'])
            ->where('hospital_id', $hospital->id)
            ->get();
        $days = Schedule::DAY_LABELS;

        return view('admin.schedules.create', compact('hospital', 'doctors', 'days'));
    }

    public function store(Request $request, Hospital $hospital)
    {
        $doctorId = $request->doctor_id;

        $request->validate([
            'doctor_id'   => 'required|exists:doctors,id',
            'day_of_week' => [
                'required',
                Rule::in(array_keys(Schedule::DAY_LABELS)),
                Rule::unique('schedules')->where(fn($q) =>
                    $q->where('doctor_id', $doctorId)
                ),
            ],
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ], [
            'day_of_week.unique' => 'Dokter sudah memiliki jadwal di hari tersebut.',
            'end_time.after'     => 'Jam selesai harus setelah jam mulai.',
        ]);

        Schedule::create($request->only([
            'doctor_id','day_of_week','start_time','end_time',
        ]));

        return redirect()->route('admin.hospitals.schedules.index', $hospital)
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Hospital $hospital, Schedule $schedule): View
    {
        $doctors = Doctor::with(['user', 'clinic'])
            ->where('hospital_id', $hospital->id)
            ->get();
        $days = Schedule::DAY_LABELS;

        return view('admin.schedules.edit', compact('hospital', 'schedule', 'doctors', 'days'));
    }

    public function update(Request $request, Hospital $hospital, Schedule $schedule)
    {
        $doctorId = $request->doctor_id;

        $request->validate([
            'doctor_id'   => 'required|exists:doctors,id',
            'day_of_week' => [
                'required',
                Rule::in(array_keys(Schedule::DAY_LABELS)),
                Rule::unique('schedules')->where(fn($q) =>
                    $q->where('doctor_id', $doctorId)
                )->ignore($schedule->id),
            ],
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($request->only([
            'doctor_id','day_of_week','start_time','end_time',
        ]));

        return redirect()->route('admin.hospitals.schedules.index', $hospital)
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Hospital $hospital, Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.hospitals.schedules.index', $hospital)
            ->with('success', 'Jadwal dihapus.');
    }
}