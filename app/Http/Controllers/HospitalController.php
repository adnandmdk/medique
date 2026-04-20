<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalController extends Controller
{
    // Halaman pilih rumah sakit
    public function select()
    {
        $hospitals = Hospital::where('is_active', true)->get();
        return view('hospital.select', compact('hospitals'));
    }

    // Simpan pilihan rumah sakit ke session
    public function choose(Request $request)
    {
        $request->validate([
            'hospital_id' => ['required', 'exists:hospitals,id'],
        ]);

        $hospital = Hospital::findOrFail($request->hospital_id);

        if (! $hospital->is_active) {
            return back()->with('error', 'Rumah sakit ini tidak aktif.');
        }

        session(['hospital_id' => $hospital->id]);

        // Redirect ke login atau dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('login');
    }

    // Ganti rumah sakit
    public function change()
    {
        session()->forget('hospital_id');
        return redirect()->route('hospital.select');
    }
}