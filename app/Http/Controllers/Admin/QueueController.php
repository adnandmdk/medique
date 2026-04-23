<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(
        private readonly QueueService $queueService
    ) {}

    public function index(Hospital $hospital): View
    {
        $queues = Queue::with([
            'patient',
            'schedule.doctor.user',
            'schedule.doctor.clinic',
        ])
        ->where('hospital_id', $hospital->id)
        ->latest()
        ->paginate(20);

        $stats = [
            'total'       => Queue::where('hospital_id', $hospital->id)->where('booking_date', today())->count(),
            'waiting'     => Queue::where('hospital_id', $hospital->id)->where('status', 'waiting')->count(),
            'called'      => Queue::where('hospital_id', $hospital->id)->where('status', 'called')->count(),
            'in_progress' => Queue::where('hospital_id', $hospital->id)->where('status', 'in_progress')->count(),
            'done'        => Queue::where('hospital_id', $hospital->id)->where('status', 'done')->where('booking_date', today())->count(),
        ];

        return view('admin.queues.index', compact('hospital', 'queues', 'stats'));
    }

    public function cancel(Hospital $hospital, Queue $queue)
    {
        $this->queueService->cancel($queue);
        return back()->with('success', 'Antrian dibatalkan.');
    }
}