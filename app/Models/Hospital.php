<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','code','address','phone',
        'email','logo','tagline','is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function activeClinics()
    {
        return $this->hasMany(Clinic::class)->where('is_active', true);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        return strtoupper(collect($words)->take(2)->map(fn($w) => $w[0])->join(''));
    }
}