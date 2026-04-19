<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'schedule_id',
        'queue_number',
        'booking_date',
        'status',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    // Status labels
    public const STATUS_LABELS = [
        'waiting'     => 'Menunggu',
        'called'      => 'Dipanggil',
        'in_progress' => 'Sedang Dilayani',
        'done'        => 'Selesai',
        'cancelled'   => 'Dibatalkan',
    ];

    // Status colors untuk badge
    public const STATUS_COLORS = [
        'waiting'     => 'bg-yellow-100 text-yellow-700',
        'called'      => 'bg-blue-100 text-blue-700',
        'in_progress' => 'bg-purple-100 text-purple-700',
        'done'        => 'bg-green-100 text-green-700',
        'cancelled'   => 'bg-red-100 text-red-700',
    ];

    // Relasi ke User (patient)
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Relasi ke Schedule
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // Relasi ke QueueLog
    public function logs()
    {
        return $this->hasMany(QueueLog::class);
    }

    // Accessor: label status
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    // Accessor: warna badge status
    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'bg-gray-100 text-gray-700';
    }

    // Generate token unik
    public static function generateToken(): string
    {
        do {
            $token = strtoupper(Str::random(8));
        } while (self::where('token', $token)->exists());

        return $token;
    }

    // Generate nomor antrian berikutnya
    public static function generateQueueNumber(int $scheduleId, string $bookingDate): int
    {
        $last = self::where('schedule_id', $scheduleId)
            ->where('booking_date', $bookingDate)
            ->whereNotIn('status', ['cancelled'])
            ->max('queue_number');

        return ($last ?? 0) + 1;
    }
}