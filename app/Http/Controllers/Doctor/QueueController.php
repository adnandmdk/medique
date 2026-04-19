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
        $queues = $this->queueService->getTodayByDoctor($doctor->id);
        return view('doctor.queues.index', compact('queues'));
    }

    public function call(Queue $queue): RedirectResponse
    {
        $this->queueService->call($queue);
        return redirect()->route('doctor.queues.index')
            ->with('success', "Antrian #{$queue->queue_number} dipanggil.");
    }

    public function start(Queue $queue): RedirectResponse
    {
        $this->queueService->start($queue);
        return redirect()->route('doctor.queues.index')
            ->with('success', "Antrian #{$queue->queue_number} sedang dilayani.");
    }

    public function finish(Queue $queue): RedirectResponse
    {
        $this->queueService->finish($queue);
        return redirect()->route('doctor.queues.index')
            ->with('success', "Antrian #{$queue->queue_number} selesai.");
    }
}