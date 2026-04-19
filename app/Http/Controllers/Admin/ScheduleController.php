<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\StoreScheduleRequest;
use App\Http\Requests\Schedule\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        private readonly ScheduleService $scheduleService
    ) {}

    public function index()
    {
    $schedules = Schedule::with(['doctor.user', 'clinic'])->paginate(10);

    return view('admin.schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        $doctors = $this->scheduleService->getAllDoctors();
        $days    = Schedule::DAY_LABELS;
        return view('admin.schedules.create', compact('doctors', 'days'));
    }

    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        $this->scheduleService->store($request);
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule): View
    {
        $doctors = $this->scheduleService->getAllDoctors();
        $days    = Schedule::DAY_LABELS;
        return view('admin.schedules.edit', compact('schedule', 'doctors', 'days'));
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $this->scheduleService->update($request, $schedule);
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $this->scheduleService->destroy($schedule);
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}