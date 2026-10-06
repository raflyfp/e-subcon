<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MonitoringApiController;

/*
|--------------------------------------------------------------------------
| API Routes - Dashboard Monitoring Management
|--------------------------------------------------------------------------
*/

Route::prefix('monitoring')->group(function () {
    // Endpoint utama: Ringkasan lengkap monitoring management (KPI, Subcon, Barang, Pekerjaan, Karyawan, Real-time feed)
    Route::get('/', [MonitoringApiController::class, 'index'])->name('api.monitoring.index');

    // Endpoint khusus KPI & Ringkasan Cepat (sangat ringan untuk polling realtime widget/screen)
    Route::get('/kpi', [MonitoringApiController::class, 'kpi'])->name('api.monitoring.kpi');

    // Endpoint khusus status karyawan (sudah/belum submit)
    Route::get('/karyawan', [MonitoringApiController::class, 'karyawan'])->name('api.monitoring.karyawan');

    // Endpoint performa per lokasi subcon
    Route::get('/subcon', [MonitoringApiController::class, 'subcon'])->name('api.monitoring.subcon');
});
