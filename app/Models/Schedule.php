<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id','day_of_week','start_time','end_time',
    ];

    public const DAY_LABELS = [
        'monday'    => 'Senin',
        'tuesday'   => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday'  => 'Kamis',
        'friday'    => 'Jumat',
        'saturday'  => 'Sabtu',
        'sunday'    => 'Minggu',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function getDayLabelAttribute(): string
    {
        return self::DAY_LABELS[$this->day_of_week] ?? $this->day_of_week;
    }

    public function getStartTimeLabelAttribute(): string
    {
        return substr($this->start_time, 0, 5);
    }

    public function getEndTimeLabelAttribute(): string
    {
        return substr($this->end_time, 0, 5);
    }

    public function scopeForHospital($query, int $hospitalId)
    {
        return $query->whereHas('doctor', fn($q) =>
            $q->where('hospital_id', $hospitalId)
        );
    }

    public function scopeOnDay($query, string $day)
    {
        return $query->where('day_of_week', $day);
    }
}