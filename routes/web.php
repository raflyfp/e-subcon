<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MasterKaryawanController;
use App\Http\Controllers\MasterLokasiSubconController;
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

// Rate limit login: 5 percobaan per menit per akun+IP
RateLimiter::for("login", function (Request $request) {
    return Limit::perMinute(5)->by(
        str($request->input("username"))->lower() . '|' . $request->ip()
    );
});

Route::get('/check-session', function () {
    return response()->json(['authenticated' => Auth::check()]);
})->name('check-session')->middleware('web');

Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('refresh-csrf')->middleware('web');

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
})->name('login')->middleware('throttle:10,1');

Route::post('/login', [UserController::class, 'login'])->name('login.post')->middleware('throttle:login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout')->middleware('throttle:50,1');


/*
|--------------------------------------------------------------------------
| ROUTE DENGAN LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD (semua role)
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/password', [UserController::class, 'password'])->name('password')->middleware('throttle:10,1');
    Route::get('/reset_password', [UserController::class, 'ChangePassword'])->name('password.change')->middleware('throttle:10,1');


    /*
    |--------------------------------------------------------------------------
    | PENGERJAAN (semua role — karyawan lihat milik sendiri, admin lihat semua)
    |--------------------------------------------------------------------------
    */

    Route::prefix('pengerjaan')->middleware('throttle:100,1')->group(function () {
        Route::get('/', [PengerjaanController::class, 'index'])->name('pengerjaan.index');
        Route::get('/riwayat', [PengerjaanController::class, 'laporan'])->name('pengerjaan.riwayat');
        Route::post('/tambah', [PengerjaanController::class, 'store'])->name('pengerjaan.store');
        Route::delete('/{id}', [PengerjaanController::class, 'destroy'])->name('pengerjaan.destroy');
    });

    Route::get('/laporan-subcon', [PengerjaanController::class, 'laporan'])->name('laporan.index')->middleware('throttle:100,1');


    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES — Master Data
    |--------------------------------------------------------------------------
    */

    Route::middleware('is_admin')->group(function () {

        // Master User
        Route::get('/user', [MasterDataController::class, 'index'])->middleware('throttle:20,1');
        Route::prefix('master-user')->middleware('throttle:20,1')->group(function () {
            Route::get('/', [MasterDataController::class, 'getData']);
            Route::post('/tambah', [MasterDataController::class, 'CreateUser'])->name('user.simpan');
        });

        // Master Karyawan
        Route::get('/karyawan', [MasterKaryawanController::class, 'index'])->middleware('throttle:20,1');
        Route::prefix('master-karyawan')->middleware('throttle:100,1')->group(function () {
            Route::get('/data', [MasterKaryawanController::class, 'getData'])->name('karyawan.data');
            Route::post('/tambah', [MasterKaryawanController::class, 'store'])->name('karyawan.store');
            Route::put('/{id}', [MasterKaryawanController::class, 'update'])->name('karyawan.update');
            Route::put('/{id}/toggle-status', [MasterKaryawanController::class, 'toggleStatus'])->name('karyawan.toggle');
        });

        // Master Barang
        Route::get('/barang', [MasterBarangController::class, 'index'])->middleware('throttle:20,1');
        Route::prefix('master-barang')->middleware('throttle:100,1')->group(function () {
            Route::post('/tambah', [MasterBarangController::class, 'store'])->name('barang.store');
            Route::put('/{id}', [MasterBarangController::class, 'update'])->name('barang.update');
            Route::put('/{id}/toggle-status', [MasterBarangController::class, 'toggleStatus'])->name('barang.toggle');
        });

        // Master Lokasi Subcon
        Route::get('/lokasi-subcon', [MasterLokasiSubconController::class, 'index'])->middleware('throttle:20,1');
        Route::prefix('master-lokasi-subcon')->middleware('throttle:100,1')->group(function () {
            Route::post('/tambah', [MasterLokasiSubconController::class, 'store'])->name('lokasi.store');
            Route::put('/{id}', [MasterLokasiSubconController::class, 'update'])->name('lokasi.update');
            Route::put('/{id}/toggle-status', [MasterLokasiSubconController::class, 'toggleStatus'])->name('lokasi.toggle');
        });
    });
});
