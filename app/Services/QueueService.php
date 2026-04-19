<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\QueueLog;
use App\Models\Schedule;
use App\Http\Requests\Queue\StoreQueueRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class QueueService
{
    // Semua antrian (admin)
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Queue::with(['patient', 'schedule.doctor.user', 'schedule.doctor.clinic'])
            ->latest()
            ->paginate($perPage);
    }

    // Antrian hari ini untuk dokter
    public function getTodayByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator
    {
        return Queue::with(['patient', 'schedule'])
            ->whereHas('schedule', fn($q) => $q->where('doctor_id', $doctorId))
            ->where('booking_date', today())
            ->orderBy('queue_number')
            ->paginate($perPage);
    }

    // Antrian milik patient
    public function getByPatient(int $patientId, int $perPage = 10): LengthAwarePaginator
    {
        return Queue::with(['schedule.doctor.user', 'schedule.doctor.clinic'])
            ->where('patient_id', $patientId)
            ->latest()
            ->paginate($perPage);
    }

    // Ambil semua jadwal aktif (untuk dropdown booking)
    public function getAvailableSchedules(): Collection
    {
        return Schedule::with(['doctor.user', 'doctor.clinic'])
            ->whereHas('doctor.clinic', fn($q) => $q->where('is_active', true))
            ->get();
    }

    // Booking antrian baru
    public function store(StoreQueueRequest $request): Queue
    {
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

        // Log: antrian dibuat
        $this->log($queue, 'called');

        return $queue;
    }

    // Panggil pasien (doctor)
    public function call(Queue $queue): Queue
    {
        $queue->update(['status' => 'called']);
        $this->log($queue, 'called');
        return $queue;
    }

    // Mulai proses (doctor)
    public function start(Queue $queue): Queue
    {
        $queue->update(['status' => 'in_progress']);
        $this->log($queue, 'started');
        return $queue;
    }

    // Selesai (doctor)
    public function finish(Queue $queue): Queue
    {
        $queue->update(['status' => 'done']);
        $this->log($queue, 'finished');
        return $queue;
    }

    // Batalkan antrian (patient / admin)
    public function cancel(Queue $queue): Queue
    {
        $queue->update(['status' => 'cancelled']);
        $this->log($queue, 'cancelled');
        return $queue;
    }

    // Tulis log aksi
    private function log(Queue $queue, string $action): void
    {
        QueueLog::create([
            'queue_id'  => $queue->id,
            'action'    => $action,
            'timestamp' => now(),
        ]);
    }
}