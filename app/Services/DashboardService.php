<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Queue;
use App\Models\User;

class DashboardService
{
    public function getAdminStats(int $hospitalId): array
    {
        return [
            'total_clinics'  => Clinic::where('hospital_id', $hospitalId)->count(),
            'active_clinics' => Clinic::where('hospital_id', $hospitalId)
                                    ->where('is_active', true)->count(),
            'total_doctors'  => Doctor::where('hospital_id', $hospitalId)->count(),
            'total_patients' => User::role('patient')
                                    ->where('hospital_id', $hospitalId)->count(),
            'today_queues'   => Queue::where('hospital_id', $hospitalId)
                                    ->where('booking_date', today())->count(),
            'waiting_queues' => Queue::where('hospital_id', $hospitalId)
                                    ->where('status', 'waiting')->count(),
            'done_queues'    => Queue::where('hospital_id', $hospitalId)
                                    ->where('status', 'done')
                                    ->whereDate('updated_at', today())->count(),
            'total_queues'   => Queue::where('hospital_id', $hospitalId)->count(),
            'recent_queues'  => Queue::with([
                                    'patient',
                                    'schedule.doctor.user',
                                    'schedule.doctor.clinic',
                                ])
                                ->where('hospital_id', $hospitalId)
                                ->latest()->take(5)->get(),
        ];
    }

    public function getDoctorStats(int $doctorId): array
    {
        $tq = Queue::whereHas('schedule', fn($q) =>
            $q->where('doctor_id', $doctorId)
        )->where('booking_date', today());

        return [
            'today_total'       => (clone $tq)->count(),
            'today_waiting'     => (clone $tq)->where('status', 'waiting')->count(),
            'today_called'      => (clone $tq)->where('status', 'called')->count(),
            'today_in_progress' => (clone $tq)->where('status', 'in_progress')->count(),
            'today_done'        => (clone $tq)->where('status', 'done')->count(),
            'next_queue'        => (clone $tq)
                                    ->whereIn('status', ['waiting', 'called'])
                                    ->orderBy('queue_number')
                                    ->with('patient')
                                    ->first(),
            'queue_list'        => (clone $tq)
                                    ->orderBy('queue_number')
                                    ->with('patient')
                                    ->get(),
        ];
    }

    public function getPatientStats(int $patientId): array
    {
        $q = Queue::where('patient_id', $patientId);

        return [
            'total_bookings' => (clone $q)->count(),
            'total_done'     => (clone $q)->where('status', 'done')->count(),
            'active_queue'   => (clone $q)
                                ->whereIn('status', ['waiting', 'called', 'in_progress'])
                                ->with(['schedule.doctor.user', 'schedule.doctor.clinic', 'logs'])
                                ->latest()->first(),
            'recent_queues'  => (clone $q)
                                ->with(['schedule.doctor.user', 'schedule.doctor.clinic'])
                                ->latest()->take(5)->get(),
        ];
    }
}