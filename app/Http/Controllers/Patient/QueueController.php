<?php
namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Queue;
use App\Models\QueueLog;
use App\Models\Schedule;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    public function index(Request $request): View
    {
        $queues = Queue::with(['schedule.doctor.user', 'schedule.doctor.clinic', 'hospital'])
            ->where('patient_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('patient.queues.index', compact('queues'));
    }

    public function create(): View
    {
        return view('patient.queues.create');
    }

    // AJAX: List semua rumah sakit aktif
    public function getHospitals(): JsonResponse
    {
        $hospitals = Hospital::where('is_active', true)
            ->select('id', 'name', 'address', 'code')
            ->get()
            ->map(fn($h) => [
                'id'       => $h->id,
                'name'     => $h->name,
                'address'  => $h->address,
                'initials' => $h->initials,
            ]);

        return response()->json($hospitals);
    }

    // AJAX: List poli berdasarkan hospital + tanggal
    public function getClinics(Request $request): JsonResponse
    {
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        $dayOfWeek = strtolower(date('l', strtotime($request->date)));

        // ✅ FIX: Query yang benar — cari poli yang punya jadwal pada hari tsb
        $clinics = Clinic::where('hospital_id', $request->hospital_id)
            ->where('is_active', true)
            ->whereHas('doctors.schedules', fn($q) =>
                $q->where('day_of_week', $dayOfWeek)
            )
            ->with(['doctors' => fn($q) =>
                $q->whereHas('schedules', fn($s) =>
                    $s->where('day_of_week', $dayOfWeek)
                )
            ])
            ->get()
            ->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->name,
                'code' => $c->poli_code,
            ]);

        return response()->json($clinics);
    }

    // AJAX: List jadwal berdasarkan poli + tanggal
    public function getSchedules(Request $request): JsonResponse
    {
        $request->validate([
            'clinic_id'   => 'required|exists:clinics,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        $dayOfWeek = strtolower(date('l', strtotime($request->date)));

        // ✅ FIX UTAMA: Query yang pasti benar
        $schedules = Schedule::with(['doctor.user', 'doctor.clinic'])
            ->whereHas('doctor', fn($q) =>
                // Filter by clinic DAN hospital
                $q->where('clinic_id', $request->clinic_id)
                  ->where('hospital_id', $request->hospital_id)
            )
            ->where('day_of_week', $dayOfWeek)
            ->get()
            ->map(function ($s) use ($request) {
                // Hitung antrian yang sudah ada hari ini untuk slot ini
                $booked = Queue::where('schedule_id', $s->id)
                    ->where('booking_date', $request->date)
                    ->whereNotIn('status', ['cancelled'])
                    ->count();

                return [
                    'id'         => $s->id,
                    'doctor'     => optional($s->doctor->user)->name ?? '—',
                    'clinic'     => optional($s->doctor->clinic)->name ?? '—',
                    'day'        => $s->day_label,
                    'start_time' => $s->start_time_label,
                    'end_time'   => $s->end_time_label,
                    'booked'     => $booked,
                ];
            });

        return response()->json($schedules);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_id'  => 'required|exists:hospitals,id',
            'schedule_id'  => 'required|exists:schedules,id',
            'booking_date' => 'required|date|after_or_equal:today',
        ], [
            'hospital_id.required'        => 'Rumah sakit wajib dipilih.',
            'schedule_id.required'        => 'Jadwal wajib dipilih.',
            'booking_date.required'       => 'Tanggal booking wajib diisi.',
            'booking_date.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
        ]);

        // ✅ Verifikasi schedule milik hospital yang dipilih
        $schedule = Schedule::whereHas('doctor', fn($q) =>
            $q->where('hospital_id', $validated['hospital_id'])
        )->findOrFail($validated['schedule_id']);

        $queueNumber = Queue::generateQueueNumber(
            $schedule->id,
            $validated['booking_date']
        );

        $queue = Queue::create([
            'hospital_id'  => $validated['hospital_id'],
            'patient_id'   => $request->user()->id,
            'schedule_id'  => $schedule->id,
            'queue_number' => $queueNumber,
            'booking_date' => $validated['booking_date'],
            'status'       => 'waiting',
            'token'        => Queue::generateToken(),
        ]);

        QueueLog::create([
            'queue_id'  => $queue->id,
            'action'    => 'called',
            'timestamp' => now(),
        ]);

        return redirect()->route('patient.queues.index')
            ->with('success', "Booking berhasil! Nomor antrian: {$queue->queue_number}");
    }

    public function cancel(Queue $queue): RedirectResponse
    {
    
    // Atau gunakan Auth facade dengan import yang benar
    $userId = Auth::id();

    abort_if($queue->patient_id !== $userId, 403);

    if (! in_array($queue->status, ['waiting'])) {
        return back()->with('error', 'Antrian tidak dapat dibatalkan.');
    }

    $this->queueService->cancel($queue);

    return redirect()->route('patient.queues.index')
        ->with('success', 'Antrian berhasil dibatalkan.');
    }
}