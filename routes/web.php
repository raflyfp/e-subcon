<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MasterKaryawanController;
use App\Http\Controllers\MasterLokasiSubconController;
use App\Http\Controllers\MasterPekerjaanController;
use App\Http\Controllers\PengerjaanController;
use App\Http\Controllers\UserController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

// Rate limit login: 20 percobaan per menit per akun/IP
RateLimiter::for("login", function (Request $request) {
    return Limit::perMinute(20)->by(
        str($request->input("username"))->lower() . '|' . $request->ip()
    );
});

// Rate limit umum untuk operasi web & AJAX berkecepatan tinggi: 1000 request per menit per user/IP
RateLimiter::for("web-traffic", function (Request $request) {
    return Limit::perMinute(1000)->by(
        $request->user()?->id ?: $request->ip()
    );
});

Route::get('/check-session', function () {
    return response()->json(['authenticated' => Auth::check()]);
})->name('check-session')->middleware(['web', 'throttle:web-traffic']);

Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('refresh-csrf')->middleware(['web', 'throttle:web-traffic']);

// Halaman utama: jika belum login langsung ke login, jika sudah login ke dashboard (admin) atau form pengerjaan (subcon)
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('pengerjaan.index');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/form-pengerjaan', function() {
    return redirect()->route('pengerjaan.index');
})->name('pengerjaan.form-public');

Route::get('/login', function () {
    if (Auth::check()) {
        if (Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('pengerjaan.index');
    }
    return view('pages.login');
})->name('login')->middleware('throttle:120,1');

Route::post('/login', [UserController::class, 'login'])->name('login.post')->middleware('throttle:login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout')->middleware('throttle:web-traffic');


/*
|--------------------------------------------------------------------------
| ROUTE DENGAN LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'throttle:web-traffic'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard');
    Route::get('/password', [UserController::class, 'password'])->name('password');
    Route::get('/reset_password', [UserController::class, 'ChangePassword'])->name('password.change');


    /*
    |--------------------------------------------------------------------------
    | PENGERJAAN & LAPORAN
    |--------------------------------------------------------------------------
    */

    Route::prefix('pengerjaan')->middleware('permission:formulir_pengerjaan')->group(function () {
        Route::get('/', [PengerjaanController::class, 'index'])->name('pengerjaan.index');
        Route::get('/riwayat', [PengerjaanController::class, 'laporan'])->name('pengerjaan.riwayat');
        Route::post('/tambah', [PengerjaanController::class, 'store'])->name('pengerjaan.store');
        Route::delete('/{id}', [PengerjaanController::class, 'destroy'])->name('pengerjaan.destroy');
    });

    Route::get('/laporan-subcon', [PengerjaanController::class, 'laporan'])->name('laporan.index')->middleware('permission:laporan_subcon');


    /*
    |--------------------------------------------------------------------------
    | MASTER DATA ROUTES (DIPROTEKSI PERMISSION)
    |--------------------------------------------------------------------------
    */

    // Master User & Hak Akses
    Route::middleware('permission:master_user')->group(function () {
        Route::get('/user', [MasterDataController::class, 'index'])->name('user.index');
        Route::prefix('master-user')->group(function () {
            Route::get('/', [MasterDataController::class, 'getData']);
            Route::post('/tambah', [MasterDataController::class, 'CreateUser'])->name('user.simpan')->middleware('permission:master_user.create');
            Route::put('/{id}', [MasterDataController::class, 'updateUser'])->name('user.update')->middleware('permission:master_user.edit');
            Route::put('/{id}/toggle-status', [MasterDataController::class, 'toggleStatus'])->name('user.toggle')->middleware('permission:master_user.toggle');
        });
    });

    // Master Karyawan
    Route::middleware('permission:master_karyawan')->group(function () {
        Route::get('/karyawan', [MasterKaryawanController::class, 'index'])->name('karyawan.index');
        Route::prefix('master-karyawan')->group(function () {
            Route::get('/data', [MasterKaryawanController::class, 'getData'])->name('karyawan.data');
            Route::post('/tambah', [MasterKaryawanController::class, 'store'])->name('karyawan.store')->middleware('permission:master_karyawan.create');
            Route::put('/{id}', [MasterKaryawanController::class, 'update'])->name('karyawan.update')->middleware('permission:master_karyawan.edit');
            Route::put('/{id}/toggle-status', [MasterKaryawanController::class, 'toggleStatus'])->name('karyawan.toggle')->middleware('permission:master_karyawan.toggle');
        });
    });

    // Master Barang
    Route::middleware('permission:master_barang')->group(function () {
        Route::get('/barang', [MasterBarangController::class, 'index'])->name('barang.index');
        Route::prefix('master-barang')->group(function () {
            Route::post('/tambah', [MasterBarangController::class, 'store'])->name('barang.store')->middleware('permission:master_barang.create');
            Route::put('/{id}', [MasterBarangController::class, 'update'])->name('barang.update')->middleware('permission:master_barang.edit');
            Route::put('/{id}/toggle-status', [MasterBarangController::class, 'toggleStatus'])->name('barang.toggle')->middleware('permission:master_barang.toggle');
        });
    });

    // Master Lokasi Subcon
    Route::middleware('permission:master_lokasi_subcon')->group(function () {
        Route::get('/lokasi-subcon', [MasterLokasiSubconController::class, 'index'])->name('lokasi.index');
        Route::prefix('master-lokasi-subcon')->group(function () {
            Route::post('/tambah', [MasterLokasiSubconController::class, 'store'])->name('lokasi.store')->middleware('permission:master_lokasi_subcon.create');
            Route::put('/{id}', [MasterLokasiSubconController::class, 'update'])->name('lokasi.update')->middleware('permission:master_lokasi_subcon.edit');
            Route::put('/{id}/toggle-status', [MasterLokasiSubconController::class, 'toggleStatus'])->name('lokasi.toggle')->middleware('permission:master_lokasi_subcon.toggle');
        });
    });

    // Master Pekerjaan
    Route::middleware('permission:master_pekerjaan')->group(function () {
        Route::get('/pekerjaan', [MasterPekerjaanController::class, 'index'])->name('pekerjaan.index');
        Route::prefix('master-pekerjaan')->group(function () {
            Route::post('/tambah', [MasterPekerjaanController::class, 'store'])->name('pekerjaan.store')->middleware('permission:master_pekerjaan.create');
            Route::put('/{id}', [MasterPekerjaanController::class, 'update'])->name('pekerjaan.update')->middleware('permission:master_pekerjaan.edit');
            Route::put('/{id}/toggle-status', [MasterPekerjaanController::class, 'toggleStatus'])->name('pekerjaan.toggle')->middleware('permission:master_pekerjaan.toggle');
        });
    });

    // Log Report (Audit Trail)
    Route::middleware('permission:log_report')->group(function () {
        Route::get('/log-report', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('log-report.index');
    });
});
