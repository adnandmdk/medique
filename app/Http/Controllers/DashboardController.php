<?php
namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user || $user->roles->isEmpty()) {
            abort(403);
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

        $query = Hospital::query();
        if ($hospitalId) $query->where('id', $hospitalId);

        $hospitals = $query->withCount([
            'clinics',
            'doctors',
            'queues as today_queues' => fn($q) => $q->where('booking_date', today()),
        ])->get();

        // Global stats
        $stats = [
            'total_hospitals' => $hospitals->count(),
            'total_doctors'   => Doctor::when($hospitalId, fn($q) => $q->where('hospital_id', $hospitalId))->count(),
            'total_patients'  => User::role('patient')->when($hospitalId, fn($q) => $q->where('hospital_id', $hospitalId))->count(),
            'today_queues'    => Queue::when($hospitalId, fn($q) => $q->where('hospital_id', $hospitalId))->where('booking_date', today())->count(),
            'waiting_queues'  => Queue::when($hospitalId, fn($q) => $q->where('hospital_id', $hospitalId))->where('status', 'waiting')->count(),
            'recent_queues'   => Queue::with(['patient','schedule.doctor.user','schedule.doctor.clinic','hospital'])
                                    ->when($hospitalId, fn($q) => $q->where('hospital_id', $hospitalId))
                                    ->latest()->take(5)->get(),
        ];

        return view('dashboard.admin', compact('stats', 'hospitals'));
    }

    private function doctorDash($user)
    {
        $doctor = $user->doctor;
        if (! $doctor) return view('dashboard.doctor', ['stats' => null]);

        $hospitalId = $doctor->hospital_id;
        $tq = Queue::whereHas('schedule', fn($q) => $q->where('doctor_id', $doctor->id))
                   ->where('booking_date', today());

        $stats = [
            'today_total'       => (clone $tq)->count(),
            'today_waiting'     => (clone $tq)->where('status','waiting')->count(),
            'today_in_progress' => (clone $tq)->where('status','in_progress')->count(),
            'today_done'        => (clone $tq)->where('status','done')->count(),
            'next_queue'        => (clone $tq)->whereIn('status',['waiting','called'])->orderBy('queue_number')->with('patient')->first(),
            'queue_list'        => (clone $tq)->orderBy('queue_number')->with('patient')->get(),
            'hospital'          => $doctor->hospital,
            'clinic'            => $doctor->clinic,
        ];

        return view('dashboard.doctor', compact('stats'));
    }

    private function patientDash($user)
    {
        $activeQueue = Queue::with(['schedule.doctor.user','schedule.doctor.clinic','hospital','logs'])
            ->where('patient_id', $user->id)
            ->whereIn('status', ['waiting','called','in_progress'])
            ->latest()->first();

        $recentQueues = Queue::with(['schedule.doctor.user','schedule.doctor.clinic','hospital'])
            ->where('patient_id', $user->id)
            ->latest()->take(5)->get();

        $totalBookings = Queue::where('patient_id', $user->id)->count();
        $totalDone     = Queue::where('patient_id', $user->id)->where('status','done')->count();

        // Estimasi posisi
        $position = null;
        if ($activeQueue && $activeQueue->status === 'waiting') {
            $position = Queue::where('schedule_id', $activeQueue->schedule_id)
                ->where('booking_date', $activeQueue->booking_date)
                ->where('status', 'waiting')
                ->where('queue_number', '<', $activeQueue->queue_number)
                ->count();
        }

        return view('dashboard.patient', compact(
            'activeQueue', 'recentQueues', 'totalBookings', 'totalDone', 'position'
        ));
    }
}