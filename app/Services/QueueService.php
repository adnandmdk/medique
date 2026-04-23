<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\QueueLog;

class QueueService
{
    public function call(Queue $queue): Queue
    {
        $queue->update(['status' => 'called']);
        $this->log($queue, 'called');
        return $queue;
    }

    public function start(Queue $queue): Queue
    {
        $queue->update(['status' => 'in_progress']);
        $this->log($queue, 'started');
        return $queue;
    }

    public function finish(Queue $queue): Queue
    {
        $queue->update(['status' => 'done']);
        $this->log($queue, 'finished');
        return $queue;
    }

    public function cancel(Queue $queue): Queue
    {
        $queue->update(['status' => 'cancelled']);
        $this->log($queue, 'cancelled');
        return $queue;
    }

    private function log(Queue $queue, string $action): void
    {
        QueueLog::create([
            'queue_id'  => $queue->id,
            'action'    => $action,
            'timestamp' => now(),
        ]);
    }
}