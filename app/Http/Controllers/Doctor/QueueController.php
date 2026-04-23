<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
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
        $doctor = $request->user()->doctor;

        if (! $doctor) {
            return view('doctor.queues.index', ['queues' => collect(), 'doctor' => null]);
        }

        $queues = Queue::with(['patient', 'schedule'])
            ->whereHas('schedule', fn($q) =>
                $q->where('doctor_id', $doctor->id)
            )
            ->where('booking_date', today())
            ->whereIn('status', ['waiting', 'called','in_progress'])
            ->orderBy('queue_number')
            ->paginate(20);

        return view('doctor.queues.index', compact('queues', 'doctor'));
    }

    public function call(Queue $queue): RedirectResponse
    {
        $this->queueService->call($queue);
        return back()->with('success', "Antrian #{$queue->queue_number} dipanggil.");
    }

    public function start(Queue $queue): RedirectResponse
    {
        $this->queueService->start($queue);
        return back()->with('success', "Antrian #{$queue->queue_number} sedang dilayani.");
    }

    public function finish(Queue $queue): RedirectResponse
    {
        $this->queueService->finish($queue);
        return back()->with('success', "Antrian #{$queue->queue_number} selesai.");
    }
}