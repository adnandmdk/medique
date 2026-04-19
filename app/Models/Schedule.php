<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    // Map hari ke bahasa Indonesia
    public const DAY_LABELS = [
        'monday'    => 'Senin',
        'tuesday'   => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday'  => 'Kamis',
        'friday'    => 'Jumat',
        'saturday'  => 'Sabtu',
        'sunday'    => 'Minggu',
    ];

    // Relasi ke Doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function clinic()
    {
    return $this->belongsTo(\App\Models\Clinic::class);
    }
    // Accessor: label hari dalam bahasa Indonesia
    public function getDayLabelAttribute(): string
    {
        return self::DAY_LABELS[$this->day_of_week] ?? $this->day_of_week;
    }

    // Accessor: format jam HH:MM
    public function getStartTimeLabelAttribute(): string
    {
        return substr($this->start_time, 0, 5);
    }

    public function getEndTimeLabelAttribute(): string
    {
        return substr($this->end_time, 0, 5);
    }
}