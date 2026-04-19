<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    // GET /api/schedules
    // GET /api/schedules?clinic_id=1
    // GET /api/schedules?day=monday
    public function index(Request $request): JsonResponse
    {
        $schedules = Schedule::with(['doctor.user', 'doctor.clinic'])
            ->when($request->clinic_id, fn($q) =>
                $q->whereHas('doctor', fn($q) =>
                    $q->where('clinic_id', $request->clinic_id)
                )
            )
            ->when($request->day, fn($q) =>
                $q->where('day_of_week', $request->day)
            )
            ->get();

        return response()->json([
            'data' => ScheduleResource::collection($schedules),
        ]);
    }

    // GET /api/schedules/{schedule}
    public function show(Schedule $schedule): JsonResponse
    {
        $schedule->load(['doctor.user', 'doctor.clinic']);

        return response()->json([
            'data' => new ScheduleResource($schedule),
        ]);
    }
}