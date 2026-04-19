<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Queue;
use Illuminate\Http\Request;

class QueueDisplayController extends Controller
{
    public function show(Request $request, ?Clinic $clinic = null)
    {
        // Default: clinic aktif pertama
        if (! $clinic) {
            $clinic = Clinic::where('is_active', true)->first();
        }

        // Antrian yang sedang dipanggil/dilayani
        $currentQueue = Queue::with(['patient', 'schedule.doctor.user', 'schedule.doctor.clinic'])
            ->when($clinic, fn($q) =>
                $q->whereHas('schedule.doctor', fn($q2) =>
                    $q2->where('clinic_id', $clinic->id)
                )
            )
            ->where('booking_date', today())
            ->whereIn('status', ['called', 'in_progress'])
            ->orderBy('queue_number')
            ->first();

        // Antrian berikutnya (waiting)
        $nextQueues = Queue::with(['patient'])
            ->when($clinic, fn($q) =>
                $q->whereHas('schedule.doctor', fn($q2) =>
                    $q2->where('clinic_id', $clinic->id)
                )
            )
            ->where('booking_date', today())
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->take(8)
            ->get();

        // Stats hari ini
        $base = Queue::when($clinic, fn($q) =>
            $q->whereHas('schedule.doctor', fn($q2) =>
                $q2->where('clinic_id', $clinic->id)
            )
        )->where('booking_date', today());

        $stats = [
            'waiting' => (clone $base)->where('status', 'waiting')->count(),
            'done'    => (clone $base)->where('status', 'done')->count(),
            'total'   => (clone $base)->count(),
        ];

        // Semua clinic aktif untuk selector
        $clinics = Clinic::where('is_active', true)->get();

        return view('display.queue-display', compact(
            'clinic', 'currentQueue', 'nextQueues', 'stats', 'clinics'
        ));
    }
}