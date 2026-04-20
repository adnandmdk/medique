<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $user       = $request->user();
        $hospitalId = session('hospital_id');

        if (! $user) return redirect()->route('login');
        if ($user->roles->isEmpty()) abort(403, 'Akun belum memiliki role.');

        // Jika belum pilih hospital, redirect
        if (! $hospitalId) return redirect()->route('hospital.select');

        return match(true) {
            $user->hasRole('admin')   => $this->adminDashboard($hospitalId),
            $user->hasRole('doctor')  => $this->doctorDashboard($user),
            $user->hasRole('patient') => $this->patientDashboard($user, $hospitalId),
            default                   => abort(403),
        };
    }

    private function adminDashboard(int $hospitalId)
    {
        $stats = $this->dashboardService->getAdminStats($hospitalId);
        return view('dashboard.admin', compact('stats'));
    }

    private function doctorDashboard($user)
    {
        $doctor = $user->doctor;
        if (! $doctor) return view('dashboard.doctor', ['stats' => null]);
        $stats = $this->dashboardService->getDoctorStats($doctor->id);
        return view('dashboard.doctor', compact('stats'));
    }

    private function patientDashboard($user, int $hospitalId)
    {
        $stats = $this->dashboardService->getPatientStats($user->id, $hospitalId);
        return view('dashboard.patient', compact('stats'));
    }
}