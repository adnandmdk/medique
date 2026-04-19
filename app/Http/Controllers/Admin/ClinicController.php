<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\StoreClinicRequest;
use App\Http\Requests\Clinic\UpdateClinicRequest;
use App\Models\Clinic;
use App\Services\ClinicService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClinicController extends Controller
{
    public function __construct(
        private readonly ClinicService $clinicService
    ) {}

    public function index()
    {
       $clinics = $this->clinicService->getAll();
    return view('admin.clinics.index', compact('clinics'));
    }

    public function create(): View
    {
        return view('admin.clinics.create');
    }

    public function store(StoreClinicRequest $request): RedirectResponse
    {
        $this->clinicService->store($request);
        return redirect()->route('admin.clinics.index')
            ->with('success', 'Poliklinik berhasil ditambahkan.');
    }

    public function edit(Clinic $clinic): View
    {
        return view('admin.clinics.edit', compact('clinic'));
    }

    public function update(UpdateClinicRequest $request, Clinic $clinic): RedirectResponse
    {
        $this->clinicService->update($request, $clinic);
        return redirect()->route('admin.clinics.index')
            ->with('success', 'Poliklinik berhasil diperbarui.');
    }

    public function destroy(Clinic $clinic): RedirectResponse
    {
        $this->clinicService->destroy($clinic);
        return redirect()->route('admin.clinics.index')
            ->with('success', 'Poliklinik berhasil dihapus.');
    }

    public function toggleStatus(Clinic $clinic): RedirectResponse
    {
        $this->clinicService->toggleStatus($clinic);
        return redirect()->route('admin.clinics.index')
            ->with('success', 'Status poliklinik berhasil diubah.');
    }
}