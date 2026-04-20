<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * 📄 TAMPILKAN PROFILE (READ ONLY / DASHBOARD STYLE)
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load(['hospital']);

        $queues = \App\Models\Queue::with([
                'schedule.doctor.user',
                'schedule.doctor.clinic'
            ])
            ->where('patient_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('profile.show', compact('user', 'queues'));
    }

    /**
     * ✏️ HALAMAN EDIT PROFILE
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', compact('user'));
    }

    /**
     * 💾 UPDATE PROFILE
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        // 🔵 VALIDASI KHUSUS PASIEN
        if ($user->isPatient()) {
            $rules['nik']           = ['nullable', 'string', 'max:20'];
            $rules['date_of_birth'] = ['nullable', 'date', 'before:today'];
            $rules['gender']        = ['nullable', 'in:male,female'];
            $rules['address']       = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }
    public function destroy(Request $request)
{
    $user = $request->user();

    Auth::logout();

    $user->delete();

    return redirect('/')->with('success', 'Akun berhasil dihapus.');
}
}