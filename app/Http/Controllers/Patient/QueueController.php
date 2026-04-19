<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Queue\StoreQueueRequest;
use App\Models\Queue;
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
        $schedules = $this->queueService->getAvailableSchedules();
        return view('patient.queues.create', compact('schedules'));
    }

    public function store(StoreQueueRequest $request): RedirectResponse
    {
        $queue = $this->queueService->store($request);
        return redirect()->route('patient.queues.index')
            ->with('success', "Booking berhasil! Nomor antrian Anda: #{$queue->queue_number} | Token: {$queue->token}");
    }

    public function cancel(Request $request, Queue $queue): RedirectResponse
    {
        // Pastikan hanya pasien pemilik antrian yang bisa cancel
        if ($queue->patient_id !== $request->user()->id) {
            abort(403);
        }

        $this->queueService->cancel($queue);
        return redirect()->route('patient.queues.index')
            ->with('success', 'Antrian berhasil dibatalkan.');
    }
}