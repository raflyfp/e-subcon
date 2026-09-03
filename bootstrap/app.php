<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\CheckPermission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'is_admin'   => IsAdmin::class,
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tangani dan catat exception ke ActivityLog
        $exceptions->respond(function (Response $response, \Throwable $exception, Request $request) {
            // 1. Catat otomatis saat validasi form gagal
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                try {
                    $uri = $request->path();
                    $module = 'System';
                    if (str_contains($uri, 'barang')) {
                        $module = 'Master Barang';
                    } elseif (str_contains($uri, 'karyawan')) {
                        $module = 'Master Karyawan';
                    } elseif (str_contains($uri, 'lokasi-subcon')) {
                        $module = 'Master Lokasi Subcon';
                    } elseif (str_contains($uri, 'pekerjaan')) {
                        $module = 'Master Pekerjaan';
                    } elseif (str_contains($uri, 'user')) {
                        $module = 'Master User';
                    } elseif (str_contains($uri, 'pengerjaan')) {
                        $module = 'Formulir Pengerjaan';
                    } elseif (str_contains($uri, 'login') || str_contains($uri, 'password')) {
                        $module = 'Autentikasi';
                    }

                    $errorsList = implode('; ', $exception->validator->errors()->all());
                    \App\Models\ActivityLog::record(
                        $module,
                        'VALIDATION_FAILED',
                        "Validasi form gagal pada [/{$uri}]: {$errorsList}"
                    );
                } catch (\Throwable $th) {
                    // Ignore logging failure
                }
            }

            // 2. Cegah tampilan 419 Page Expired dan alihkan langsung ke login
            if ($response->getStatusCode() === 419 || $exception instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan coba login kembali.');
            }

            return $response;
        });
    })->create();
