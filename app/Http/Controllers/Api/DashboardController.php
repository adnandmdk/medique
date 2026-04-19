<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return match(true) {
            $user->hasRole('admin')   => $this->adminStats(),
            $user->hasRole('doctor')  => $this->doctorStats($user),
            $user->hasRole('patient') => $this->patientStats($user),
            default                   => response()->json(['message' => 'Unauthorized.'], 403),
        };
    }

    private function adminStats(): JsonResponse
    {
        $stats = $this->dashboardService->getAdminStats();
        unset($stats['recent_queues']); // exclude collection, send separately

        return response()->json(['data' => $stats]);
    }

    private function doctorStats($user): JsonResponse
    {
        $doctor = $user->doctor;

        if (! $doctor) {
            return response()->json(['message' => 'Data dokter tidak ditemukan.'], 404);
        }

        $stats = $this->dashboardService->getDoctorStats($doctor->id);
        unset($stats['queue_list'], $stats['next_queue']);

        return response()->json(['data' => $stats]);
    }

    private function patientStats($user): JsonResponse
    {
        $stats = $this->dashboardService->getPatientStats($user->id);
        unset($stats['recent_queues'], $stats['active_queue']);

        return response()->json(['data' => $stats]);
    }
}