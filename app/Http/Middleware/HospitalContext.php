<?php

namespace App\Http\Middleware;

use App\Models\Hospital;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HospitalContext
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil hospital dari session
        $hospitalId = session('hospital_id');

        if (! $hospitalId) {
            // Jika sudah login tapi belum pilih hospital → redirect pilih
            if (Auth::check() && ! $request->routeIs('hospital.*')) {
                return redirect()->route('hospital.select');
            }
            return $next($request);
        }

        $hospital = Hospital::where('id', $hospitalId)
            ->where('is_active', true)
            ->first();

        if (! $hospital) {
            session()->forget('hospital_id');
            return redirect()->route('hospital.select')
                ->with('error', 'Rumah sakit tidak ditemukan.');
        }

        // Share ke semua view
        view()->share('currentHospital', $hospital);

        // Bind ke request untuk digunakan di controller
        $request->merge(['_hospital' => $hospital]);

        return $next($request);
    }
}