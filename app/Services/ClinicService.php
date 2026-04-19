<?php

namespace App\Services;

use App\Models\Clinic;
use App\Http\Requests\Clinic\StoreClinicRequest;
use App\Http\Requests\Clinic\UpdateClinicRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class ClinicService
{
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Clinic::latest()->paginate(10);
    }

    public function store(StoreClinicRequest $request): Clinic
    {
        return Clinic::create([
            'name'      => $request->name,
            'location'  => $request->location,
            'is_active' => $request->boolean('is_active', true),
        ]);
    }

    public function update(UpdateClinicRequest $request, Clinic $clinic): Clinic
    {
        $clinic->update([
            'name'      => $request->name,
            'location'  => $request->location,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return $clinic;
    }

    public function destroy(Clinic $clinic): void
    {
        $clinic->delete();
    }

    public function toggleStatus(Clinic $clinic): Clinic
    {
        $clinic->update(['is_active' => ! $clinic->is_active]);
        return $clinic;
    }
}