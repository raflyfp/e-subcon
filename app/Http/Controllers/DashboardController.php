<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\LokasiSubcon;
use App\Models\Pengerjaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Halaman Dashboard & Monitoring Pengisian Form Karyawan (dengan Sorting, Filter Status & Pagination)
     */
    public function index(Request $request)
    {
        $user  = auth()->user();
        $today = now()->toDateString();

        // Tanggal yang sedang dipantau (default: hari ini)
        $tanggal      = $request->input('tanggal', $today);
        $prevDate     = \Carbon\Carbon::parse($tanggal)->subDay()->toDateString();
        $nextDate     = \Carbon\Carbon::parse($tanggal)->addDay()->toDateString();
        $search       = $request->input('search');
        $statusFilter = $request->input('status'); // 'sudah', 'belum', or null
        $sortBy       = $request->input('sort', 'nama_asc'); // 'nama_asc', 'nama_desc', 'belum_dulu', 'sudah_dulu', 'submit_desc'

        if ($user->is_admin) {
            // Admin: monitoring seluruh karyawan di sistem atau filter per lokasi subcon
            $baseQuery = Karyawan::with('lokasiSubcon')
                ->where('is_active', true);

            if ($request->filled('lokasi_subcon_id')) {
                $baseQuery->where('lokasi_subcon_id', $request->lokasi_subcon_id);
            }

            $lokasiList = LokasiSubcon::where('is_active', true)->orderBy('nama_lokasi')->get();
            $subcon     = null;
        } else {
            // Akun Subcon: monitoring khusus karyawan di subcon tersebut
            $subcon   = $user->lokasiSubcon;
            $subconId = $subcon?->id;

            $baseQuery = Karyawan::where('is_active', true);

            if ($subconId) {
                $baseQuery->where('lokasi_subcon_id', $subconId);
            } else {
                $baseQuery->whereRaw('1 = 0');
            }

            $lokasiList = collect([]);
        }

        // Ambil ID seluruh karyawan untuk menghitung ringkasan statistik
        $allKaryawanIds = (clone $baseQuery)->pluck('id')->toArray();
        $totalPengerjaanCount = Pengerjaan::whereIn('karyawan_id', $allKaryawanIds)
            ->whereDate('tanggal', $tanggal)
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        $sudahMengisiCount = $totalPengerjaanCount;
        $belumMengisiCount = count($allKaryawanIds) - $sudahMengisiCount;

        // Query utama dengan hitungan submit_count pada tanggal terpilih
        $karyawanQuery = (clone $baseQuery)
            ->withCount(['pengerjaan as submit_count' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }]);

        // Filter search nama / no karyawan jika ada
        if ($search) {
            $karyawanQuery->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('no_karyawan', 'like', "%{$search}%");
            });
        }

        // Filter Status: Sudah Isi / Belum Isi
        if ($statusFilter === 'sudah') {
            $karyawanQuery->whereHas('pengerjaan', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            });
        } elseif ($statusFilter === 'belum') {
            $karyawanQuery->whereDoesntHave('pengerjaan', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            });
        }

        // Sorting
        switch ($sortBy) {
            case 'belum_dulu':
                $karyawanQuery->orderBy('submit_count', 'asc')->orderBy('nama_karyawan', 'asc');
                break;
            case 'sudah_dulu':
                $karyawanQuery->orderBy('submit_count', 'desc')->orderBy('nama_karyawan', 'asc');
                break;
            case 'nama_desc':
                $karyawanQuery->orderBy('nama_karyawan', 'desc');
                break;
            case 'submit_desc':
                $karyawanQuery->orderBy('submit_count', 'desc')->orderBy('nama_karyawan', 'asc');
                break;
            case 'nama_asc':
            default:
                $karyawanQuery->orderBy('nama_karyawan', 'asc');
                break;
        }

        // Paginate daftar karyawan (10 data per halaman)
        $karyawanList = $karyawanQuery->paginate(10)->withQueryString();

        // Ambil data pengerjaan pada tanggal terpilih untuk karyawan di halaman aktif
        $pageKaryawanIds = $karyawanList->pluck('id')->toArray();
        $pengerjaanTanggal = Pengerjaan::with('barang')
            ->whereIn('karyawan_id', $pageKaryawanIds)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('karyawan_id');

        return view('pages.dashboard', compact(
            'subcon',
            'tanggal',
            'prevDate',
            'nextDate',
            'today',
            'search',
            'statusFilter',
            'sortBy',
            'karyawanList',
            'pengerjaanTanggal',
            'sudahMengisiCount',
            'belumMengisiCount',
            'lokasiList'
        ));
    }
}
