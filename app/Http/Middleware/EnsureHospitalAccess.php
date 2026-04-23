<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHospitalAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ FIXED: Gunakan $request->user() bukan auth()->user()
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Super admin bisa akses semua
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Doctor: otomatis bind ke hospital dokternya
        if ($user->isDoctor() && $user->doctor) {
            $hospitalId = $user->doctor->hospital_id;
            $request->merge(['_hospital_id' => $hospitalId]);
            view()->share('currentHospitalId', $hospitalId);
            return $next($request);
        }

        // Admin & Patient: dari hospital_id di user
        if ($user->hospital_id) {
            $request->merge(['_hospital_id' => $user->hospital_id]);
            view()->share('currentHospitalId', $user->hospital_id);
            return $next($request);
        }

        return $next($request);
    }
}