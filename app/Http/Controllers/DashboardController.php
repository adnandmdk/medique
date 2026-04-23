<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Queue;
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

        if (! $user || $user->roles->isEmpty()) {
            abort(403, 'Akun belum memiliki role.');
        }

        return match(true) {
            $user->isSuperAdmin() || $user->isAdmin() => $this->adminDash($user),
            $user->isDoctor()                         => $this->doctorDash($user),
            $user->isPatient()                        => $this->patientDash($user),
            default                                   => abort(403),
        };
    }

    private function adminDash($user)
    {
        $hospitalId = $user->isSuperAdmin() ? null : $user->hospital_id;

        $hospitalsQuery = Hospital::query();
        if ($hospitalId) $hospitalsQuery->where('id', $hospitalId);

        $hospitals = $hospitalsQuery->withCount([
            'clinics',
            'doctors',
            'queues as today_queues' => fn($q) =>
                $q->where('booking_date', today()),
        ])->get();

        $stats = $hospitalId
            ? $this->dashboardService->getAdminStats($hospitalId)
            : [
                'total_clinics'  => 0,
                'active_clinics' => 0,
                'total_doctors'  => 0,
                'total_patients' => 0,
                'today_queues'   => Queue::where('booking_date', today())->count(),
                'waiting_queues' => Queue::where('status', 'waiting')->count(),
                'done_queues'    => 0,
                'total_queues'   => Queue::count(),
                'recent_queues'  => Queue::with(['patient','schedule.doctor.user','schedule.doctor.clinic'])
                                    ->latest()->take(5)->get(),
            ];

        return view('dashboard.admin', compact('stats', 'hospitals'));
    }

    private function doctorDash($user)
    {
        $doctor = $user->doctor;
        if (! $doctor) {
            return view('dashboard.doctor', ['stats' => null]);
        }

        $stats = $this->dashboardService->getDoctorStats($doctor->id);
        $stats['hospital'] = $doctor->hospital;
        $stats['clinic']   = $doctor->clinic;

        return view('dashboard.doctor', compact('stats'));
    }

    private function patientDash($user)
    {
        $patientStats = $this->dashboardService->getPatientStats($user->id);

        $activeQueue   = $patientStats['active_queue'];
        $recentQueues  = $patientStats['recent_queues'];
        $totalBookings = $patientStats['total_bookings'];
        $totalDone     = $patientStats['total_done'];

        $position = null;
        if ($activeQueue && $activeQueue->status === 'waiting') {
            $position = Queue::where('schedule_id', $activeQueue->schedule_id)
                ->where('booking_date', $activeQueue->booking_date)
                ->where('status', 'waiting')
                ->where('queue_number', '<', $activeQueue->queue_number)
                ->count();
        }

        return view('dashboard.patient', compact(
            'activeQueue', 'recentQueues',
            'totalBookings', 'totalDone', 'position'
        ));
    }
}