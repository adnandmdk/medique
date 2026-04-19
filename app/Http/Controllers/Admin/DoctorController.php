<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Services\DoctorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function __construct(
        private readonly DoctorService $doctorService
    ) {}

    public function index(): View
    {
         $doctors = Doctor::with('clinic')->paginate(10);
    return view('admin.doctors.index', compact('doctors'));
    }

    public function create(): View
    {
        $users   = $this->doctorService->getAvailableUsers();
        $clinics = $this->doctorService->getActiveClinics();
        return view('admin.doctors.create', compact('users', 'clinics'));
    }

    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $this->doctorService->store($request);
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Doctor $doctor): View
    {
        $users   = $this->doctorService->getAvailableUsers();
        $clinics = $this->doctorService->getActiveClinics();
        return view('admin.doctors.edit', compact('doctor', 'users', 'clinics'));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->doctorService->update($request, $doctor);
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $this->doctorService->destroy($doctor);
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Dokter berhasil dihapus.');
    }
}