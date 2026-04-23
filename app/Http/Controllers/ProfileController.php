<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user   = $request->user()->load('hospital');
        $queues = Queue::with(['schedule.doctor.user', 'schedule.doctor.clinic'])
            ->where('patient_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('profile.show', compact('user', 'queues'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user  = $request->user();
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        if ($user->isPatient()) {
            $rules['nik']           = ['nullable', 'string', 'max:20'];
            $rules['date_of_birth'] = ['nullable', 'date', 'before:today'];
            $rules['gender']        = ['nullable', 'in:male,female'];
            $rules['address']       = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);
        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}