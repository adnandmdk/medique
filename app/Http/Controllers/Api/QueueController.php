<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QueueResource;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class QueueController extends Controller
{
    public function __construct(
        private readonly QueueService $queueService
    ) {}

    // GET /api/queues — riwayat antrian patient
    public function index(Request $request): JsonResponse
    {
        $queues = Queue::with(['schedule.doctor.user', 'schedule.doctor.clinic'])
            ->where('patient_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => QueueResource::collection($queues),
            'meta' => [
                'current_page' => $queues->currentPage(),
                'last_page'    => $queues->lastPage(),
                'total'        => $queues->total(),
            ],
        ]);
    }

    // POST /api/queues — booking antrian
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id'  => ['required', 'exists:schedules,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $queue = Queue::create([
            'patient_id'   => $request->user()->id,
            'schedule_id'  => $request->schedule_id,
            'queue_number' => Queue::generateQueueNumber(
                $request->schedule_id,
                $request->booking_date
            ),
            'booking_date' => $request->booking_date,
            'status'       => 'waiting',
            'token'        => Queue::generateToken(),
        ]);

        $queue->load(['schedule.doctor.user', 'schedule.doctor.clinic']);

        return response()->json([
            'message' => 'Booking berhasil.',
            'data'    => new QueueResource($queue),
        ], 201);
    }

    // GET /api/queues/{queue} — detail antrian
    public function show(Request $request, Queue $queue): JsonResponse
    {
        // Pastikan patient hanya bisa lihat antrian sendiri
        if ($request->user()->hasRole('patient') &&
            $queue->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $queue->load(['patient', 'schedule.doctor.user', 'schedule.doctor.clinic', 'logs']);

        return response()->json([
            'data' => new QueueResource($queue),
        ]);
    }

    // PATCH /api/queues/{queue}/cancel — patient cancel
    public function cancel(Request $request, Queue $queue): JsonResponse
    {
        if ($queue->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (! in_array($queue->status, ['waiting'])) {
            return response()->json([
                'message' => 'Antrian tidak dapat dibatalkan.',
            ], 422);
        }

        $this->queueService->cancel($queue);

        return response()->json([
            'message' => 'Antrian berhasil dibatalkan.',
            'data'    => new QueueResource($queue->fresh()),
        ]);
    }

    // GET /api/doctor/queues — antrian hari ini untuk dokter
    public function doctorQueue(Request $request): JsonResponse
    {
        $doctor = $request->user()->doctor;

        if (! $doctor) {
            return response()->json(['message' => 'Data dokter tidak ditemukan.'], 404);
        }

        $queues = Queue::with(['patient', 'schedule'])
            ->whereHas('schedule', fn($q) => $q->where('doctor_id', $doctor->id))
            ->where('booking_date', today())
            ->orderBy('queue_number')
            ->get();

        return response()->json([
            'data' => QueueResource::collection($queues),
        ]);
    }

    // PATCH /api/doctor/queues/{queue}/call
    public function call(Queue $queue): JsonResponse
    {
        $this->queueService->call($queue);

        return response()->json([
            'message' => "Antrian #{$queue->queue_number} dipanggil.",
            'data'    => new QueueResource($queue->fresh()),
        ]);
    }

    // PATCH /api/doctor/queues/{queue}/start
    public function start(Queue $queue): JsonResponse
    {
        $this->queueService->start($queue);

        return response()->json([
            'message' => "Antrian #{$queue->queue_number} sedang dilayani.",
            'data'    => new QueueResource($queue->fresh()),
        ]);
    }

    // PATCH /api/doctor/queues/{queue}/finish
    public function finish(Queue $queue): JsonResponse
    {
        $this->queueService->finish($queue);

        return response()->json([
            'message' => "Antrian #{$queue->queue_number} selesai.",
            'data'    => new QueueResource($queue->fresh()),
        ]);
    }
}