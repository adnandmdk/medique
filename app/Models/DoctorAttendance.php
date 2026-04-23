<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorAttendance extends Model
{
    protected $fillable = ['doctor_id','date','is_present'];

    protected function casts(): array
    {
        return ['is_present' => 'boolean', 'date' => 'date'];
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}