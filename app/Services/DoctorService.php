<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Clinic;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DoctorService
{
    // ✅ FIX — tambah with(['user', 'clinic'])
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Doctor::with(['user', 'clinic'])
            ->latest()
            ->paginate($perPage);
    }

    public function getAvailableUsers(): Collection
    {
        $usedUserIds = Doctor::pluck('user_id');

        return User::role('doctor')
            ->whereNotIn('id', $usedUserIds)
            ->get();
    }

    public function getActiveClinics(): Collection
    {
        return Clinic::active()->get();
    }

    public function store(StoreDoctorRequest $request): Doctor
    {
        return Doctor::create([
            'user_id'        => $request->user_id,
            'clinic_id'      => $request->clinic_id,
            'specialization' => $request->specialization,
            'licence_number' => $request->licence_number,
        ]);
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): Doctor
    {
        $doctor->update([
            'user_id'        => $request->user_id,
            'clinic_id'      => $request->clinic_id,
            'specialization' => $request->specialization,
            'licence_number' => $request->licence_number,
        ]);

        return $doctor;
    }

    public function destroy(Doctor $doctor): void
    {
        $doctor->delete();
    }
}