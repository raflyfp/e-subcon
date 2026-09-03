<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek status akun aktif
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi administrator.');
        }

        // Cek hak akses ke permission yang diminta
        if (!$user->canAccess($permission)) {
            \App\Models\ActivityLog::record(
                'Hak Akses',
                'ACCESS_DENIED',
                "Upaya akses ditolak: Pengguna {$user->name} ({$user->role}) mencoba mengakses fitur '{$permission}' tanpa izin."
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk fitur ini.',
                ], 403);
            }

            // Tentukan target fallback yang diizinkan untuk user
            $targetRoute = $user->canAccess('dashboard') ? 'dashboard' : 'pengerjaan.index';

            return redirect()->route($targetRoute)->with('error', 'Akses Ditolak: Anda tidak memiliki hak akses untuk halaman tersebut.');
        }

        return $next($request);
    }
}
