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
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->roles->isEmpty()) {
            abort(403, 'Akun belum memiliki role. Hubungi Admin.');
        }

        return match(true) {
            $user->hasRole('admin')   => $this->adminDashboard(),
            $user->hasRole('doctor')  => $this->doctorDashboard($user),
            $user->hasRole('patient') => $this->patientDashboard($user),
            default                   => abort(403, 'Role tidak dikenali.')
        };
    }

    private function adminDashboard()
    {
        $stats = $this->dashboardService->getAdminStats();
        return view('dashboard.admin', compact('stats'));
    }

    private function doctorDashboard($user)
    {
        $doctor = $user->doctor;

        if (! $doctor) {
            return view('dashboard.doctor', ['stats' => null]);
        }

        $stats = $this->dashboardService->getDoctorStats($doctor->id);
        return view('dashboard.doctor', compact('stats'));
    }

    private function patientDashboard($user)
    {
        $stats = $this->dashboardService->getPatientStats($user->id);
        return view('dashboard.patient', compact('stats'));
    }
}