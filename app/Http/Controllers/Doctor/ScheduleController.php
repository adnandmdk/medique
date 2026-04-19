<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        private readonly ScheduleService $scheduleService
    ) {}

    public function index(Request $request): View
    {
        // Ambil data doctor dari user yang login
        $doctor    = $request->user()->doctor;
        $schedules = $this->scheduleService->getByDoctor($doctor);

        return view('doctor.schedules.index', compact('schedules'));
    }
}