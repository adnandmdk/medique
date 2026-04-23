<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'hospital_id','name','email','password',
        'phone','role','nik','date_of_birth',
        'gender','address','is_super_admin',
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'date_of_birth'     => 'date',
            'is_super_admin'    => 'boolean',
        ];
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isDoctor(): bool
    {
        return $this->hasRole('doctor');
    }

    public function isPatient(): bool
    {
        return $this->hasRole('patient');
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            'male'   => 'Laki-laki',
            'female' => 'Perempuan',
            default  => '—',
        };
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}