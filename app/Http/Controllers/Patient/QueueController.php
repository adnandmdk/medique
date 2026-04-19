<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Queue\StoreQueueRequest;
use App\Models\Doctor;
use App\Models\Queue;
use App\Models\Schedule;
use App\Services\QueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(
        private readonly QueueService $queueService
    ) {}

    public function index(Request $request): View
    {
        $queues = $this->queueService->getByPatient($request->user()->id);
        return view('patient.queues.index', compact('queues'));
    }

    public function create(): View
    {
        // Hanya kirim doctors + schedules per doctor (bukan semua schedule)
        $doctors = Doctor::with(['user', 'clinic', 'schedules'])
            ->whereHas('clinic', fn($q) => $q->where('is_active', true))
            ->get();

        // Map: doctor_id => schedules array (untuk JS)
        $doctorSchedules = [];
        foreach ($doctors as $doctor) {
            $doctorSchedules[$doctor->id] = $doctor->schedules->map(fn($s) => [
                'id'          => $s->id,
                'day_of_week' => $s->day_of_week,
                'day_label'   => $s->day_label,
                'start_time'  => $s->start_time,
                'end_time'    => $s->end_time,
            ])->values();
        }

        return view('patient.queues.create', compact('doctors', 'doctorSchedules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'doctor_id'    => ['required', 'exists:doctors,id'],
            'schedule_id'  => ['required', 'exists:schedules,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'doctor_id.required'    => 'Dokter wajib dipilih.',
            'schedule_id.required'  => 'Jadwal wajib dipilih.',
            'booking_date.required' => 'Tanggal booking wajib diisi.',
            'booking_date.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
        ]);

        // Pastikan schedule milik doctor yang dipilih
        $schedule = Schedule::where('id', $request->schedule_id)
            ->where('doctor_id', $request->doctor_id)
            ->firstOrFail();

        $queue = Queue::create([
            'patient_id'   => $request->user()->id,
            'schedule_id'  => $schedule->id,
            'queue_number' => Queue::generateQueueNumber($schedule->id, $request->booking_date),
            'booking_date' => $request->booking_date,
            'status'       => 'waiting',
            'token'        => Queue::generateToken(),
        ]);

        // Log
        \App\Models\QueueLog::create([
            'queue_id'  => $queue->id,
            'action'    => 'called',
            'timestamp' => now(),
        ]);

        return redirect()->route('patient.queues.index')
            ->with('success', "Booking berhasil! Nomor antrian Anda: #{$queue->queue_number}");
    }

    public function cancel(Queue $queue): RedirectResponse
    {
        if ($queue->patient_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $this->queueService->cancel($queue);

        return redirect()->route('patient.queues.index')
            ->with('success', 'Antrian berhasil dibatalkan.');
    }
}