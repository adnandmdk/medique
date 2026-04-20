<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id', 'name', 'code', 'location', 'is_active',
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

    // Auto-generate kode poli dari nama jika tidak diisi
    public function getCodeAttribute($value): string
    {
        if ($value) return strtoupper($value);

        // Auto: ambil huruf kapital dari nama
        preg_match_all('/[A-Z]/', $this->name, $matches);
        $code = implode('', array_slice($matches[0], 0, 3));
        return $code ?: strtoupper(substr($this->name, 0, 2));
    }
}