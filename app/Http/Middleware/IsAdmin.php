<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!auth()->check() || auth()->user()->is_admin != 1) {
            return redirect()->route('dashboard')->with('unauthorized', [
                'type' => 'error',
                'title' => 'Akses Ditolak',
                'text' => 'Anda Tidak Memiliki Hak Akses',
                'timer' => 2000
            ]);
        }

        return $next($request);
    }
}
