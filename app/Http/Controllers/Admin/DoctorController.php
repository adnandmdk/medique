<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DoctorController extends Controller
{
    public function index(Hospital $hospital): View
    {
        $doctors = Doctor::with(['user', 'clinic'])
            ->where('hospital_id', $hospital->id)
            ->paginate(15);

        return view('admin.doctors.index', compact('hospital', 'doctors'));
    }

    public function create(Hospital $hospital): View
    {
        $clinics = Clinic::where('hospital_id', $hospital->id)
            ->where('is_active', true)->get();

        $usedIds = Doctor::where('hospital_id', $hospital->id)->pluck('user_id');
        $users   = User::role('doctor')
            ->whereNotIn('id', $usedIds)
            ->get();

        return view('admin.doctors.create', compact('hospital', 'clinics', 'users'));
    }

    public function store(Request $request, Hospital $hospital)
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,id|unique:doctors,user_id',
            'clinic_id'      => 'required|exists:clinics,id',
            'specialization' => 'required|string|max:100',
            'licence_number' => 'required|string|max:50|unique:doctors,licence_number',
        ], [
            'user_id.unique'          => 'User ini sudah terdaftar sebagai dokter.',
            'licence_number.unique'   => 'Nomor lisensi sudah terdaftar.',
        ]);

        $data['hospital_id'] = $hospital->id;
        Doctor::create($data);

        // Update hospital_id user dokter
        User::where('id', $data['user_id'])
            ->update(['hospital_id' => $hospital->id]);

        return redirect()->route('admin.hospitals.doctors.index', $hospital)
            ->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Hospital $hospital, Doctor $doctor): View
    {
        $clinics = Clinic::where('hospital_id', $hospital->id)
            ->where('is_active', true)->get();

        $usedIds = Doctor::where('hospital_id', $hospital->id)
            ->where('id', '!=', $doctor->id)->pluck('user_id');
        $role = Role::where('name', 'doctor')->first();

$users = $role
    ? User::role('doctor')->whereNotIn('id', $usedIds)->get()
    : collect(); // kosong kalau role belum ada

        return view('admin.doctors.edit', compact('hospital', 'doctor', 'clinics', 'users'));
    }

    public function update(Request $request, Hospital $hospital, Doctor $doctor)
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,id|unique:doctors,user_id,'.$doctor->id,
            'clinic_id'      => 'required|exists:clinics,id',
            'specialization' => 'required|string|max:100',
            'licence_number' => 'required|string|max:50|unique:doctors,licence_number,'.$doctor->id,
        ]);

        $doctor->update($data);

        return redirect()->route('admin.hospitals.doctors.index', $hospital)
            ->with('success', 'Data dokter diperbarui.');
    }

    public function destroy(Hospital $hospital, Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('admin.hospitals.doctors.index', $hospital)
            ->with('success', 'Dokter dihapus.');
    }
}