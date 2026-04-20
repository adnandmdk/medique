<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\QueueLog;
use App\Models\Schedule;
use App\Services\QueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    public function index(Request $request): View
    {
        $queues = $this->queueService->getByPatient($request->user()->id);
        return view('patient.queues.index', compact('queues'));
    }

    public function create(Request $request): View
    {
        $hospitalId = session('hospital_id');

        // Ambil jadwal yang tersedia (filter per rumah sakit)
        $schedules = Schedule::with(['doctor.user', 'doctor.clinic'])
            ->whereHas('doctor.clinic', fn($q) =>
                $q->where('hospital_id', $hospitalId)
                  ->where('is_active', true)
            )
            ->get()
            ->groupBy('doctor_id'); // group by dokter

        return view('patient.queues.create', compact('schedules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $hospitalId = session('hospital_id');

        $request->validate([
            'schedule_id'  => ['required', 'exists:schedules,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'schedule_id.required'        => 'Jadwal wajib dipilih.',
            'booking_date.required'       => 'Tanggal booking wajib diisi.',
            'booking_date.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
        ]);

        // Pastikan schedule valid untuk hospital ini
        $schedule = Schedule::whereHas('doctor.clinic', fn($q) =>
            $q->where('hospital_id', $hospitalId)
        )->findOrFail($request->schedule_id);

        $queueNumber = Queue::generateQueueNumber($schedule->id, $request->booking_date);

        $queue = Queue::create([
            'hospital_id'  => $hospitalId,
            'patient_id'   => $request->user()->id,
            'schedule_id'  => $schedule->id,
            'queue_number' => $queueNumber,
            'booking_date' => $request->booking_date,
            'status'       => 'waiting',
            'token'        => Queue::generateToken(),
        ]);

        QueueLog::create([
            'queue_id'  => $queue->id,
            'action'    => 'called',
            'timestamp' => now(),
        ]);

        return redirect()->route('patient.queues.index')
            ->with('success', "Booking berhasil! Nomor antrian Anda: {$queue->queue_number}");
    }

    public function cancel(Request $request, Queue $queue): RedirectResponse
    {
        if ($queue->patient_id !== $request->user()->id) abort(403);
        $this->queueService->cancel($queue);
        return redirect()->route('patient.queues.index')
            ->with('success', 'Antrian berhasil dibatalkan.');
    }
}