<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateHospitalScope
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ FIXED: Gunakan $request->user() bukan auth()->user()
        $user = $request->user();

        if ($user && ! $user->isSuperAdmin() && ! $user->hospital_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda belum terhubung ke rumah sakit. Hubungi Admin.');
        }

        return $next($request);
    }
}