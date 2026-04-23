<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;   // ✅ import DB facade
use Illuminate\Support\Str;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id','patient_id','schedule_id',
        'queue_number','booking_date','status','token',
    ];

    protected function casts(): array
    {
        return ['booking_date' => 'date'];
    }

    public const STATUS_LABELS = [
        'waiting'     => 'Menunggu',
        'called'      => 'Dipanggil',
        'in_progress' => 'Sedang Dilayani',
        'done'        => 'Selesai',
        'cancelled'   => 'Dibatalkan',
    ];

    public const STATUS_COLORS = [
        'waiting'     => 'badge-waiting',
        'called'      => 'badge-called',
        'in_progress' => 'badge-progress',
        'done'        => 'badge-done',
        'cancelled'   => 'badge-cancelled',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function logs()
    {
        return $this->hasMany(QueueLog::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'badge-inactive';
    }

    public static function generateToken(): string
    {
        do {
            $token = strtoupper(Str::random(8));
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Generate nomor antrian format: PU-0001
     * ✅ FIXED: Gunakan DB:: facade setelah di-import
     */
    public static function generateQueueNumber(int $scheduleId, string $bookingDate): string
    {
        $schedule = Schedule::with('doctor.clinic')->findOrFail($scheduleId);
        $clinic   = optional(optional($schedule->doctor)->clinic);
        $poliCode = $clinic->poli_code ?? 'XX';
        $clinicId = $clinic->id ?? 0;

        // ✅ DB:: sudah di-import, tidak perlu backslash
        return DB::transaction(function () use ($bookingDate, $poliCode, $clinicId) {
            $count = self::whereHas('schedule.doctor', fn($q) =>
                        $q->where('clinic_id', $clinicId)
                    )
                    ->where('booking_date', $bookingDate)
                    ->whereNotIn('status', ['cancelled'])
                    ->lockForUpdate()
                    ->count();

            return $poliCode . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function scopeForHospital($query, int $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    public function scopeToday($query)
    {
        return $query->where('booking_date', today());
    }
}