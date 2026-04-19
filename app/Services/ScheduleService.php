<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Http\Requests\Schedule\StoreScheduleRequest;
use App\Http\Requests\Schedule\UpdateScheduleRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ScheduleService
{
    // ✅ FIX — tambah with(['doctor.user', 'doctor.clinic'])
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Schedule::with(['doctor.user', 'doctor.clinic'])
            ->latest()
            ->paginate($perPage);
    }

    public function getByDoctor(Doctor $doctor, int $perPage = 10): LengthAwarePaginator
    {
        return Schedule::with(['doctor.user', 'doctor.clinic'])
            ->where('doctor_id', $doctor->id)
            ->orderByRaw("FIELD(day_of_week,
                'monday','tuesday','wednesday',
                'thursday','friday','saturday','sunday'
            )")
            ->paginate($perPage);
    }

    // ✅ FIX — tambah with(['user', 'clinic'])
    public function getAllDoctors(): Collection
    {
        return Doctor::with(['user', 'clinic'])->get();
    }

    public function store(StoreScheduleRequest $request): Schedule
    {
        return Schedule::create([
            'doctor_id'   => $request->doctor_id,
            'day_of_week' => $request->day_of_week,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule): Schedule
    {
        $schedule->update([
            'doctor_id'   => $request->doctor_id,
            'day_of_week' => $request->day_of_week,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return $schedule;
    }

    public function destroy(Schedule $schedule): void
    {
        $schedule->delete();
    }
}