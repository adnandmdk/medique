<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicController extends Controller
{
    public function index(Hospital $hospital): View
    {
        $clinics = Clinic::where('hospital_id', $hospital->id)
            ->withCount('doctors')
            ->paginate(15);

        return view('admin.clinics.index', compact('hospital', 'clinics'));
    }

    public function create(Hospital $hospital): View
    {
        return view('admin.clinics.create', compact('hospital'));
    }

    public function store(Request $request, Hospital $hospital)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'nullable|string|max:10',
            'location'  => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['hospital_id'] = $hospital->id;
        $data['is_active']   = $request->boolean('is_active', true);

        Clinic::create($data);

        return redirect()->route('admin.hospitals.clinics.index', $hospital)
            ->with('success', 'Poliklinik berhasil ditambahkan.');
    }

    public function edit(Hospital $hospital, Clinic $clinic): View
    {
        return view('admin.clinics.edit', compact('hospital', 'clinic'));
    }

    public function update(Request $request, Hospital $hospital, Clinic $clinic)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'nullable|string|max:10',
            'location'  => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $clinic->update($data);

        return redirect()->route('admin.hospitals.clinics.index', $hospital)
            ->with('success', 'Poliklinik berhasil diperbarui.');
    }

    public function destroy(Hospital $hospital, Clinic $clinic)
    {
        $clinic->delete();
        return redirect()->route('admin.hospitals.clinics.index', $hospital)
            ->with('success', 'Poliklinik dihapus.');
    }

    public function toggleStatus(Hospital $hospital, Clinic $clinic)
    {
        $clinic->update(['is_active' => ! $clinic->is_active]);
        return back()->with('success', 'Status poliklinik diperbarui.');
    }
}