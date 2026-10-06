<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Karyawan;
use App\Models\LokasiSubcon;
use App\Models\Pengerjaan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringApiController extends Controller
{
    /**
     * Helper autentikasi & autorisasi API
     * Mendukung:
     * 1. Sesi login Laravel (Web Session)
     * 2. API Key via Header 'X-API-KEY', 'Authorization: Bearer <key>', atau Query Param 'api_key'
     */
    protected function authenticateApi(Request $request): array
    {
        // 1. Cek Sesi Web Auth jika user sedang login
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->is_active) {
                return ['authorized' => false, 'error' => 'Akun pengguna dinonaktifkan.', 'status' => 403];
            }
            return [
                'authorized' => true,
                'user'       => $user,
                'is_admin'   => (bool) $user->is_admin,
                'subcon_id'  => $user->is_admin ? null : $user->lokasiSubcon?->id,
            ];
        }

        // 2. Cek API Key untuk integrasi eksternal (TV Display, Dashboard Management terpisah, dll)
        $configuredKey = config('app.monitoring_api_key', env('MONITORING_API_KEY', 'subcon-management-monitoring-2026'));
        $incomingKey   = $request->header('X-API-KEY')
            ?: $request->bearerToken()
            ?: $request->query('api_key');

        if ($incomingKey && hash_equals($configuredKey, $incomingKey)) {
            return [
                'authorized' => true,
                'user'       => null,
                'is_admin'   => true, // API Key manajemen memiliki akses penuh ke seluruh lokasi
                'subcon_id'  => null,
            ];
        }

        return [
            'authorized' => false,
            'error'      => 'Akses Ditolak: Anda belum login atau API Key yang disertakan tidak valid. Gunakan header "X-API-KEY" atau login terlebih dahulu.',
            'status'     => 401,
        ];
    }

    /**
     * Format durasi menit ke bentuk string "X Jam Y Menit"
     */
    protected function formatDurasi(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0 Menit';
        }

        $jam = intdiv($minutes, 60);
        $sisa = $minutes % 60;

        if ($jam > 0 && $sisa > 0) {
            return "{$jam} Jam {$sisa} Menit";
        } elseif ($jam > 0) {
            return "{$jam} Jam";
        } else {
            return "{$sisa} Menit";
        }
    }

    /**
     * Endpoint Utama: Dashboard Monitoring Management Lengkap
     * GET /api/monitoring
     */
    public function index(Request $request): JsonResponse
    {
        $auth = $this->authenticateApi($request);
        if (!$auth['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $auth['error'],
            ], $auth['status']);
        }

        $today        = now()->toDateString();
        $tanggal      = $request->input('tanggal', $today);
        $tanggalMulai = $request->input('tanggal_mulai', $tanggal);
        $tanggalAkhir = $request->input('tanggal_akhir', $tanggal);
        $search       = $request->input('search');
        $statusFilter = $request->input('status'); // 'sudah', 'belum', or null
        $lokasiId     = $auth['is_admin'] ? $request->input('lokasi_subcon_id') : $auth['subcon_id'];

        // 1. Query Dasar Karyawan Aktif
        $karyawanQuery = Karyawan::with('lokasiSubcon')
            ->where('is_active', true);

        if ($lokasiId) {
            $karyawanQuery->where('lokasi_subcon_id', $lokasiId);
        }

        if ($search) {
            $karyawanQuery->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('no_karyawan', 'like', "%{$search}%");
            });
        }

        $allKaryawan = $karyawanQuery->orderBy('nama_karyawan')->get();
        $allKaryawanIds = $allKaryawan->pluck('id')->toArray();

        // 2. Query Transaksi Pengerjaan pada rentang tanggal
        $pengerjaanQuery = Pengerjaan::with(['barang', 'lokasiSubcon', 'karyawan'])
            ->whereIn('karyawan_id', $allKaryawanIds);

        if ($tanggalMulai === $tanggalAkhir) {
            $pengerjaanQuery->whereDate('tanggal', $tanggalMulai);
        } else {
            $pengerjaanQuery->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
        }

        $allPengerjaan = $pengerjaanQuery->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group pengerjaan per karyawan
        $pengerjaanByKaryawan = $allPengerjaan->groupBy('karyawan_id');

        // 3. Hitung KPI Ringkasan
        $totalKaryawan = count($allKaryawan);
        $sudahMengisiKaryawanIds = $allPengerjaan->pluck('karyawan_id')->unique()->toArray();
        $sudahMengisiCount = count($sudahMengisiKaryawanIds);
        $belumMengisiCount = max(0, $totalKaryawan - $sudahMengisiCount);
        $persentasePengisian = $totalKaryawan > 0 ? round(($sudahMengisiCount / $totalKaryawan) * 100, 1) : 0;

        $totalOutputPcs   = (int) $allPengerjaan->sum('jumlah');
        $totalDurasiMenit = (int) $allPengerjaan->sum('durasi_menit');
        $totalTransaksi   = count($allPengerjaan);

        // Subcon scope
        $subconScopeQuery = LokasiSubcon::where('is_active', true);
        if ($lokasiId) {
            $subconScopeQuery->where('id', $lokasiId);
        }
        $subconList = $subconScopeQuery->orderBy('nama_lokasi')->get();

        $kpi = [
            'tanggal'                => $tanggal,
            'tanggal_mulai'          => $tanggalMulai,
            'tanggal_akhir'          => $tanggalAkhir,
            'is_single_date'         => ($tanggalMulai === $tanggalAkhir),
            'total_subcon_aktif'     => $subconList->count(),
            'total_karyawan_aktif'   => $totalKaryawan,
            'sudah_mengisi'          => $sudahMengisiCount,
            'belum_mengisi'          => $belumMengisiCount,
            'persentase_pengisian'   => $persentasePengisian,
            'total_output_pcs'       => $totalOutputPcs,
            'total_durasi_menit'     => $totalDurasiMenit,
            'total_durasi_formatted' => $this->formatDurasi($totalDurasiMenit),
            'total_transaksi'        => $totalTransaksi,
        ];

        // 4. Breakdown Per Subcon
        $perSubcon = $subconList->map(function ($sc) use ($allKaryawan, $allPengerjaan) {
            $karyawanSubcon = $allKaryawan->where('lokasi_subcon_id', $sc->id);
            $karyawanSubconIds = $karyawanSubcon->pluck('id')->toArray();
            $totKaryawan = count($karyawanSubcon);

            $pengerjaanSubcon = $allPengerjaan->whereIn('karyawan_id', $karyawanSubconIds);
            $sudahIsi = $pengerjaanSubcon->pluck('karyawan_id')->unique()->count();
            $belumIsi = max(0, $totKaryawan - $sudahIsi);
            $pct = $totKaryawan > 0 ? round(($sudahIsi / $totKaryawan) * 100, 1) : 0;

            $outputPcs = (int) $pengerjaanSubcon->sum('jumlah');
            $durasiMenit = (int) $pengerjaanSubcon->sum('durasi_menit');

            return [
                'subcon_id'              => $sc->id,
                'nama_lokasi'            => $sc->nama_lokasi,
                'alamat'                 => $sc->alamat,
                'total_karyawan'         => $totKaryawan,
                'sudah_mengisi'          => $sudahIsi,
                'belum_mengisi'          => $belumIsi,
                'persentase_pengisian'   => $pct,
                'total_output_pcs'       => $outputPcs,
                'total_durasi_menit'     => $durasiMenit,
                'total_durasi_formatted' => $this->formatDurasi($durasiMenit),
                'total_transaksi'        => count($pengerjaanSubcon),
            ];
        })->values();

        // 5. Breakdown Per Barang
        $perBarang = $allPengerjaan->groupBy('barang_id')->map(function ($items, $barangId) {
            $first = $items->first();
            $barang = $first->barang;
            $qty = (int) $items->sum('jumlah');
            $durasi = (int) $items->sum('durasi_menit');

            return [
                'barang_id'              => $barangId,
                'kode_barang'            => $barang?->kode_barang ?: '-',
                'nama_barang'            => $barang?->nama_barang ?: 'Barang #' . $barangId,
                'satuan'                 => $barang?->satuan ?: 'PCS',
                'total_pcs'              => $qty,
                'total_durasi_menit'     => $durasi,
                'total_durasi_formatted' => $this->formatDurasi($durasi),
                'total_transaksi'        => count($items),
                'total_karyawan'         => $items->pluck('karyawan_id')->unique()->count(),
            ];
        })->values()->sortByDesc('total_pcs')->values();

        // 6. Breakdown Per Jenis Pekerjaan
        $perPekerjaan = $allPengerjaan->groupBy(function ($item) {
            return $item->jenis_pekerjaan ?: 'LAINNYA';
        })->map(function ($items, $namaPekerjaan) {
            $qty = (int) $items->sum('jumlah');
            $durasi = (int) $items->sum('durasi_menit');

            return [
                'jenis_pekerjaan'        => $namaPekerjaan,
                'total_pcs'              => $qty,
                'total_durasi_menit'     => $durasi,
                'total_durasi_formatted' => $this->formatDurasi($durasi),
                'total_transaksi'        => count($items),
            ];
        })->values()->sortByDesc('total_pcs')->values();

        // 7. Daftar Karyawan & Status Kehadiran
        $karyawanList = $allKaryawan->map(function ($k) use ($pengerjaanByKaryawan) {
            $items = $pengerjaanByKaryawan->get($k->id, collect([]));
            $isSudah = $items->isNotEmpty();
            $totalPcs = (int) $items->sum('jumlah');
            $totalDurasi = (int) $items->sum('durasi_menit');

            return [
                'karyawan_id'            => $k->id,
                'no_karyawan'            => $k->no_karyawan,
                'nama_karyawan'          => $k->nama_karyawan,
                'lokasi_subcon'          => [
                    'id'   => $k->lokasiSubcon?->id,
                    'nama' => $k->lokasiSubcon?->nama_lokasi,
                ],
                'status'                 => $isSudah ? 'sudah' : 'belum',
                'submit_count'           => count($items),
                'total_pcs'              => $totalPcs,
                'total_durasi_menit'     => $totalDurasi,
                'total_durasi_formatted' => $this->formatDurasi($totalDurasi),
                'pengerjaan'             => $items->map(function ($p) {
                    return [
                        'id'              => $p->id,
                        'tanggal'         => $p->tanggal ? Carbon::parse($p->tanggal)->format('Y-m-d') : null,
                        'barang_id'       => $p->barang_id,
                        'kode_barang'     => $p->barang?->kode_barang,
                        'nama_barang'     => $p->barang?->nama_barang,
                        'satuan'          => $p->barang?->satuan ?: 'PCS',
                        'jenis_pekerjaan' => $p->jenis_pekerjaan,
                        'jam_mulai'       => $p->jam_mulai ? substr($p->jam_mulai, 0, 5) : null,
                        'jam_selesai'     => $p->jam_selesai ? substr($p->jam_selesai, 0, 5) : null,
                        'durasi_menit'    => (int) $p->durasi_menit,
                        'durasi_text'     => $p->durasi_text,
                        'jumlah'          => (int) $p->jumlah,
                        'keterangan'      => $p->keterangan,
                    ];
                })->values(),
            ];
        });

        // Filter status jika diminta
        if ($statusFilter === 'sudah') {
            $karyawanList = $karyawanList->where('status', 'sudah')->values();
        } elseif ($statusFilter === 'belum') {
            $karyawanList = $karyawanList->where('status', 'belum')->values();
        }

        // 8. Recent Activities (15 transaksi pengerjaan terbaru)
        $recentActivities = $allPengerjaan->take(15)->map(function ($p) {
            return [
                'id'              => $p->id,
                'tanggal'         => $p->tanggal ? Carbon::parse($p->tanggal)->format('Y-m-d') : null,
                'jam_mulai'       => $p->jam_mulai ? substr($p->jam_mulai, 0, 5) : null,
                'jam_selesai'     => $p->jam_selesai ? substr($p->jam_selesai, 0, 5) : null,
                'durasi_menit'    => (int) $p->durasi_menit,
                'durasi_text'     => $p->durasi_text,
                'karyawan'        => [
                    'id'   => $p->karyawan_id,
                    'no'   => $p->karyawan?->no_karyawan,
                    'nama' => $p->karyawan?->nama_karyawan,
                ],
                'barang'          => [
                    'id'     => $p->barang_id,
                    'kode'   => $p->barang?->kode_barang,
                    'nama'   => $p->barang?->nama_barang,
                    'satuan' => $p->barang?->satuan ?: 'PCS',
                ],
                'lokasi_subcon'   => $p->lokasiSubcon?->nama_lokasi,
                'jenis_pekerjaan' => $p->jenis_pekerjaan,
                'jumlah'          => (int) $p->jumlah,
                'keterangan'      => $p->keterangan,
                'created_at'      => $p->created_at?->toIso8601String(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard monitoring management berhasil dimuat',
            'meta'    => [
                'timestamp'  => now()->toIso8601String(),
                'version'    => 'v1',
                'parameters' => [
                    'tanggal'          => $tanggal,
                    'tanggal_mulai'    => $tanggalMulai,
                    'tanggal_akhir'    => $tanggalAkhir,
                    'lokasi_subcon_id' => $lokasiId ? (int) $lokasiId : null,
                    'status'           => $statusFilter,
                    'search'           => $search,
                ],
            ],
            'data'    => [
                'kpi'               => $kpi,
                'per_subcon'        => $perSubcon,
                'per_barang'        => $perBarang,
                'per_pekerjaan'     => $perPekerjaan,
                'karyawan'          => $karyawanList,
                'recent_activities' => $recentActivities,
            ],
        ]);
    }

    /**
     * Endpoint Ringan: KPI Saja (Sangat cocok untuk live-polling widget / TV screen)
     * GET /api/monitoring/kpi
     */
    public function kpi(Request $request): JsonResponse
    {
        $auth = $this->authenticateApi($request);
        if (!$auth['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $auth['error'],
            ], $auth['status']);
        }

        $today        = now()->toDateString();
        $tanggal      = $request->input('tanggal', $today);
        $tanggalMulai = $request->input('tanggal_mulai', $tanggal);
        $tanggalAkhir = $request->input('tanggal_akhir', $tanggal);
        $lokasiId     = $auth['is_admin'] ? $request->input('lokasi_subcon_id') : $auth['subcon_id'];

        $karyawanQuery = Karyawan::where('is_active', true);
        if ($lokasiId) {
            $karyawanQuery->where('lokasi_subcon_id', $lokasiId);
        }
        $allKaryawanIds = $karyawanQuery->pluck('id')->toArray();
        $totalKaryawan  = count($allKaryawanIds);

        $pengerjaanQuery = Pengerjaan::whereIn('karyawan_id', $allKaryawanIds);
        if ($tanggalMulai === $tanggalAkhir) {
            $pengerjaanQuery->whereDate('tanggal', $tanggalMulai);
        } else {
            $pengerjaanQuery->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
        }

        $allPengerjaan = $pengerjaanQuery->get(['karyawan_id', 'jumlah', 'durasi_menit']);
        $sudahMengisi  = $allPengerjaan->pluck('karyawan_id')->unique()->count();
        $belumMengisi  = max(0, $totalKaryawan - $sudahMengisi);
        $pct           = $totalKaryawan > 0 ? round(($sudahMengisi / $totalKaryawan) * 100, 1) : 0;
        $totalPcs      = (int) $allPengerjaan->sum('jumlah');
        $totalDurasi   = (int) $allPengerjaan->sum('durasi_menit');

        return response()->json([
            'success' => true,
            'message' => 'KPI monitoring berhasil dimuat',
            'meta'    => [
                'timestamp' => now()->toIso8601String(),
                'tanggal'   => $tanggal,
            ],
            'data'    => [
                'total_karyawan_aktif'   => $totalKaryawan,
                'sudah_mengisi'          => $sudahMengisi,
                'belum_mengisi'          => $belumMengisi,
                'persentase_pengisian'   => $pct,
                'total_output_pcs'       => $totalPcs,
                'total_durasi_menit'     => $totalDurasi,
                'total_durasi_formatted' => $this->formatDurasi($totalDurasi),
                'total_transaksi'        => count($allPengerjaan),
            ],
        ]);
    }

    /**
     * Endpoint Khusus Karyawan & Status Kehadiran
     * GET /api/monitoring/karyawan
     */
    public function karyawan(Request $request): JsonResponse
    {
        $auth = $this->authenticateApi($request);
        if (!$auth['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $auth['error'],
            ], $auth['status']);
        }

        $today        = now()->toDateString();
        $tanggal      = $request->input('tanggal', $today);
        $search       = $request->input('search');
        $statusFilter = $request->input('status');
        $lokasiId     = $auth['is_admin'] ? $request->input('lokasi_subcon_id') : $auth['subcon_id'];

        $karyawanQuery = Karyawan::with('lokasiSubcon')
            ->where('is_active', true);

        if ($lokasiId) {
            $karyawanQuery->where('lokasi_subcon_id', $lokasiId);
        }

        if ($search) {
            $karyawanQuery->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('no_karyawan', 'like', "%{$search}%");
            });
        }

        $karyawans = $karyawanQuery->orderBy('nama_karyawan')->get();
        $karyawanIds = $karyawans->pluck('id')->toArray();

        $pengerjaan = Pengerjaan::with('barang')
            ->whereIn('karyawan_id', $karyawanIds)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->groupBy('karyawan_id');

        $result = $karyawans->map(function ($k) use ($pengerjaan) {
            $items = $pengerjaan->get($k->id, collect([]));
            $isSudah = $items->isNotEmpty();

            return [
                'karyawan_id'            => $k->id,
                'no_karyawan'            => $k->no_karyawan,
                'nama_karyawan'          => $k->nama_karyawan,
                'lokasi_subcon'          => $k->lokasiSubcon?->nama_lokasi,
                'status'                 => $isSudah ? 'sudah' : 'belum',
                'submit_count'           => count($items),
                'total_pcs'              => (int) $items->sum('jumlah'),
                'total_durasi_menit'     => (int) $items->sum('durasi_menit'),
                'total_durasi_formatted' => $this->formatDurasi((int) $items->sum('durasi_menit')),
                'items'                  => $items->map(function ($item) {
                    return [
                        'barang_kode'     => $item->barang?->kode_barang,
                        'barang_nama'     => $item->barang?->nama_barang,
                        'jumlah'          => (int) $item->jumlah,
                        'satuan'          => $item->barang?->satuan ?: 'PCS',
                        'durasi'          => $item->durasi_text,
                        'jenis_pekerjaan' => $item->jenis_pekerjaan,
                    ];
                })->values(),
            ];
        });

        if ($statusFilter === 'sudah') {
            $result = $result->where('status', 'sudah')->values();
        } elseif ($statusFilter === 'belum') {
            $result = $result->where('status', 'belum')->values();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan monitoring berhasil dimuat',
            'meta'    => [
                'tanggal' => $tanggal,
                'total'   => count($result),
            ],
            'data'    => $result,
        ]);
    }

    /**
     * Endpoint Khusus Performa Per Lokasi Subcon
     * GET /api/monitoring/subcon
     */
    public function subcon(Request $request): JsonResponse
    {
        $auth = $this->authenticateApi($request);
        if (!$auth['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $auth['error'],
            ], $auth['status']);
        }

        $today        = now()->toDateString();
        $tanggal      = $request->input('tanggal', $today);
        $tanggalMulai = $request->input('tanggal_mulai', $tanggal);
        $tanggalAkhir = $request->input('tanggal_akhir', $tanggal);
        $lokasiId     = $auth['is_admin'] ? $request->input('lokasi_subcon_id') : $auth['subcon_id'];

        $subconQuery = LokasiSubcon::with(['karyawan' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true);

        if ($lokasiId) {
            $subconQuery->where('id', $lokasiId);
        }

        $subcons = $subconQuery->orderBy('nama_lokasi')->get();

        $allPengerjaan = Pengerjaan::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->get(['lokasi_subcon_id', 'karyawan_id', 'jumlah', 'durasi_menit']);

        $result = $subcons->map(function ($sc) use ($allPengerjaan) {
            $karyawanCount = $sc->karyawan->count();
            $karyawanIds = $sc->karyawan->pluck('id')->toArray();

            $pengerjaanSc = $allPengerjaan->where('lokasi_subcon_id', $sc->id);
            $sudahCount = $pengerjaanSc->pluck('karyawan_id')->unique()->count();
            $belumCount = max(0, $karyawanCount - $sudahCount);
            $pct = $karyawanCount > 0 ? round(($sudahCount / $karyawanCount) * 100, 1) : 0;

            $totalPcs = (int) $pengerjaanSc->sum('jumlah');
            $totalDurasi = (int) $pengerjaanSc->sum('durasi_menit');

            return [
                'subcon_id'              => $sc->id,
                'nama_lokasi'            => $sc->nama_lokasi,
                'alamat'                 => $sc->alamat,
                'total_karyawan'         => $karyawanCount,
                'sudah_mengisi'          => $sudahCount,
                'belum_mengisi'          => $belumCount,
                'persentase_pengisian'   => $pct,
                'total_output_pcs'       => $totalPcs,
                'total_durasi_menit'     => $totalDurasi,
                'total_durasi_formatted' => $this->formatDurasi($totalDurasi),
                'total_transaksi'        => count($pengerjaanSc),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data performa per lokasi subcon berhasil dimuat',
            'meta'    => [
                'tanggal' => $tanggal,
                'total'   => count($result),
            ],
            'data'    => $result,
        ]);
    }

    /**
     * Endpoint Khusus Daftar Transaksi Semua Pengerjaan
     * GET /api/monitoring/pengerjaan
     */
    public function pengerjaan(Request $request): JsonResponse
    {
        $auth = $this->authenticateApi($request);
        if (!$auth['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $auth['error'],
            ], $auth['status']);
        }

        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $tanggal      = $request->input('tanggal');
        $lokasiId     = $auth['is_admin'] ? $request->input('lokasi_subcon_id') : $auth['subcon_id'];
        $karyawanId   = $request->input('karyawan_id');
        $barangId     = $request->input('barang_id');
        $search       = $request->input('search');
        $perPage      = $request->input('per_page');

        $query = Pengerjaan::with(['karyawan', 'barang', 'lokasiSubcon']);

        if ($lokasiId) {
            $query->where('lokasi_subcon_id', $lokasiId);
        }

        if ($karyawanId) {
            $query->where('karyawan_id', $karyawanId);
        }

        if ($barangId) {
            $query->where('barang_id', $barangId);
        }

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        } elseif ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
        } elseif ($tanggalMulai) {
            $query->whereDate('tanggal', '>=', $tanggalMulai);
        } elseif ($tanggalAkhir) {
            $query->whereDate('tanggal', '<=', $tanggalAkhir);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('karyawan', function ($qk) use ($search) {
                    $qk->where('nama_karyawan', 'like', "%{$search}%")
                       ->orWhere('no_karyawan', 'like', "%{$search}%");
                })->orWhereHas('barang', function ($qb) use ($search) {
                    $qb->where('nama_barang', 'like', "%{$search}%")
                       ->orWhere('kode_barang', 'like', "%{$search}%");
                })->orWhere('jenis_pekerjaan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        $formatter = function ($p) {
            return [
                'id'              => $p->id,
                'tanggal'         => $p->tanggal ? Carbon::parse($p->tanggal)->format('Y-m-d') : null,
                'jam_mulai'       => $p->jam_mulai ? substr($p->jam_mulai, 0, 5) : null,
                'jam_selesai'     => $p->jam_selesai ? substr($p->jam_selesai, 0, 5) : null,
                'durasi_menit'    => (int) $p->durasi_menit,
                'durasi_text'     => $p->durasi_text,
                'lokasi_subcon'   => [
                    'id'   => $p->lokasi_subcon_id,
                    'nama' => $p->lokasiSubcon?->nama_lokasi,
                ],
                'karyawan'        => [
                    'id'   => $p->karyawan_id,
                    'no'   => $p->karyawan?->no_karyawan,
                    'nama' => $p->karyawan?->nama_karyawan,
                ],
                'barang'          => [
                    'id'     => $p->barang_id,
                    'kode'   => $p->barang?->kode_barang,
                    'nama'   => $p->barang?->nama_barang,
                    'satuan' => $p->barang?->satuan ?: 'PCS',
                ],
                'jenis_pekerjaan' => $p->jenis_pekerjaan,
                'jumlah'          => (int) $p->jumlah,
                'keterangan'      => $p->keterangan,
                'created_at'      => $p->created_at?->toIso8601String(),
            ];
        };

        if ($perPage && $perPage !== 'all') {
            $paginated = $query->paginate((int) $perPage);
            $items = collect($paginated->items())->map($formatter);
            return response()->json([
                'success' => true,
                'message' => 'Data pengerjaan berhasil dimuat',
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                    'per_page'     => $paginated->perPage(),
                    'total'        => $paginated->total(),
                ],
                'data' => $items,
            ]);
        }

        $allPengerjaan = $query->get();
        $items = $allPengerjaan->map($formatter);

        return response()->json([
            'success' => true,
            'message' => 'Data semua pengerjaan berhasil dimuat',
            'meta'    => [
                'total_transaksi'    => $items->count(),
                'total_output_pcs'   => (int) $allPengerjaan->sum('jumlah'),
                'total_durasi_menit' => (int) $allPengerjaan->sum('durasi_menit'),
            ],
            'data'    => $items,
        ]);
    }
}
