<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Queue;
use App\Models\User;

class DashboardService
{
    // ═══════════════════════════════
    // ADMIN STATS
    // ═══════════════════════════════
    public function getAdminStats(): array
    {
        return [
            'total_clinics'  => Clinic::count(),
            'active_clinics' => Clinic::where('is_active', true)->count(),
            'total_doctors'  => Doctor::count(),
            'total_patients' => User::role('patient')->count(),
            'total_queues'   => Queue::count(),
            'today_queues'   => Queue::where('booking_date', today())->count(),
            'waiting_queues' => Queue::where('status', 'waiting')->count(),
            'done_queues'    => Queue::where('status', 'done')
                                     ->whereDate('updated_at', today())
                                     ->count(),
            'recent_queues'  => Queue::with([
                                    'patient',
                                    'schedule.doctor.user',
                                    'schedule.doctor.clinic',
                                ])
                                ->latest()
                                ->take(5)
                                ->get(),
        ];
    }

    // ═══════════════════════════════
    // DOCTOR STATS
    // ═══════════════════════════════
    public function getDoctorStats(int $doctorId): array
    {
        $todayQueues = Queue::whereHas('schedule', fn($q) =>
            $q->where('doctor_id', $doctorId)
        )->where('booking_date', today());

        return [
            'today_total'       => (clone $todayQueues)->count(),
            'today_waiting'     => (clone $todayQueues)->where('status', 'waiting')->count(),
            'today_called'      => (clone $todayQueues)->where('status', 'called')->count(),
            'today_in_progress' => (clone $todayQueues)->where('status', 'in_progress')->count(),
            'today_done'        => (clone $todayQueues)->where('status', 'done')->count(),
            'next_queue'        => (clone $todayQueues)
                                    ->whereIn('status', ['waiting', 'called'])
                                    ->orderBy('queue_number')
                                    ->with('patient')
                                    ->first(),
            'queue_list'        => (clone $todayQueues)
                                    ->orderBy('queue_number')
                                    ->with('patient')
                                    ->get(),
        ];
    }

    // ═══════════════════════════════
    // PATIENT STATS
    // ═══════════════════════════════
    public function getPatientStats(int $patientId): array
    {
        $queues = Queue::where('patient_id', $patientId);

        return [
            'total_bookings' => (clone $queues)->count(),
            'active_queue'   => (clone $queues)
                                    ->whereIn('status', ['waiting', 'called', 'in_progress'])
                                    ->with(['schedule.doctor.user', 'schedule.doctor.clinic'])
                                    ->latest()
                                    ->first(),
            'recent_queues'  => (clone $queues)
                                    ->with(['schedule.doctor.user', 'schedule.doctor.clinic'])
                                    ->latest()
                                    ->take(5)
                                    ->get(),
        ];
    }
}