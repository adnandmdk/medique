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

    public function hospital() { return $this->belongsTo(Hospital::class); }
    public function doctors()  { return $this->hasMany(Doctor::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function scopeForHospital($query, int $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    // Auto generate kode dari nama
    public function getPoliCodeAttribute(): string
    {
        if ($this->code) return strtoupper($this->code);
        // Ambil huruf awal setiap kata
        $words = explode(' ', $this->name);
        $code  = collect($words)->map(fn($w) => strtoupper($w[0]))->join('');
        return substr($code, 0, 4);
    }
}