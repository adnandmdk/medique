<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ tambah ini

class ValidateHospitalScope
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ Gunakan $request->user() bukan auth()->user()
        $user = $request->user();

        if ($user && ! $user->isSuperAdmin() && ! $user->hospital_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda belum terhubung ke rumah sakit.');
        }

        return $next($request);
    }
}