<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id','name','code','location','is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHospital($query, int $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    // Auto-generate kode poli dari nama jika tidak ada
    public function getPoliCodeAttribute(): string
    {
        if ($this->code) return strtoupper($this->code);

        $words = explode(' ', $this->name);
        return strtoupper(
            collect($words)->map(fn($w) => $w[0] ?? '')->join('')
        );
    }
}