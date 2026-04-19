<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(
        private readonly QueueService $queueService
    ) {}

    public function index(): View
    {
        $queues = $this->queueService->getAll();
        return view('admin.queues.index', compact('queues'));
    }

    public function cancel(Queue $queue): RedirectResponse
    {
        $this->queueService->cancel($queue);
        return redirect()->route('admin.queues.index')
            ->with('success', 'Antrian berhasil dibatalkan.');
    }
}