<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Karyawan;
use App\Models\LokasiSubcon;
use App\Models\Pengerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengerjaanController extends Controller
{
    /**
     * Dashboard — ringkasan data pengerjaan
     */
    public function dashboard()
    {
        $user  = auth()->user();
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        if ($user->is_admin) {
            // Admin: lihat ringkasan seluruh data sistem
            $pengerjaanHariIni  = Pengerjaan::where('tanggal', $today)->sum('jumlah');
            $pengerjaanBulanIni = Pengerjaan::where('tanggal', '>=', $monthStart)->sum('jumlah');
            $totalSubcon        = LokasiSubcon::where('is_active', true)->count();
            $totalKaryawan      = Karyawan::where('is_active', true)->count();
            $totalBarang        = Barang::where('is_active', true)->count();

            return view('pages.dashboard', compact(
                'pengerjaanHariIni',
                'pengerjaanBulanIni',
                'totalSubcon',
                'totalKaryawan',
                'totalBarang'
            ));
        }

        // Akun Subcon: lihat ringkasan khusus subcon tersebut
        $subcon = $user->lokasiSubcon;
        $subconId = $subcon?->id;

        $pengerjaanHariIni  = 0;
        $pengerjaanBulanIni = 0;
        $totalKaryawan      = 0;
        $totalBarang        = 0;

        if ($subconId) {
            $pengerjaanHariIni  = Pengerjaan::where('lokasi_subcon_id', $subconId)->where('tanggal', $today)->sum('jumlah');
            $pengerjaanBulanIni = Pengerjaan::where('lokasi_subcon_id', $subconId)->where('tanggal', '>=', $monthStart)->sum('jumlah');
            $totalKaryawan      = Karyawan::where('lokasi_subcon_id', $subconId)->where('is_active', true)->count();
            
            $assignedBarangCount = $subcon->barang()->where('is_active', true)->count();
            $totalBarang        = $assignedBarangCount > 0 ? $assignedBarangCount : Barang::where('is_active', true)->count();
        }

        return view('pages.dashboard', compact(
            'pengerjaanHariIni',
            'pengerjaanBulanIni',
            'totalKaryawan',
            'totalBarang',
            'subcon'
        ));
    }

    /**
     * Halaman Formulir Input Pengerjaan Barang (Wajib Login)
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->is_admin) {
            // Admin dapat memilih seluruh karyawan, barang, dan lokasi subcon
            $karyawanList = Karyawan::where('is_active', true)->orderBy('nama_karyawan')->get();
            $barangList   = Barang::with('lokasiSubcon')->where('is_active', true)->orderBy('nama_barang')->get();
            $lokasiList   = LokasiSubcon::where('is_active', true)->orderBy('nama_lokasi')->get();
            $subcon       = null;

            return view('pages.pengerjaan', compact(
                'karyawanList',
                'barangList',
                'lokasiList',
                'subcon'
            ));
        }

        // Akun Subcon: Karyawan dan barang sesuai subcon yang login
        $subcon = $user->lokasiSubcon;
        $subconId = $subcon?->id;

        // Ambil karyawan yang terdaftar pada subcon ini (fallback ke semua karyawan aktif jika belum diatur)
        $karyawanList = Karyawan::where('lokasi_subcon_id', $subconId)
            ->where('is_active', true)
            ->orderBy('nama_karyawan')
            ->get();

        if ($karyawanList->isEmpty()) {
            $karyawanList = Karyawan::where('is_active', true)->orderBy('nama_karyawan')->get();
        }

        // Ambil barang yang ditugaskan ke subcon ini (fallback ke semua barang aktif jika belum diatur)
        $barangList = collect([]);
        if ($subcon) {
            $barangList = $subcon->barang()->where('is_active', true)->orderBy('nama_barang')->get();
        }
        if ($barangList->isEmpty()) {
            $barangList = Barang::where('is_active', true)->orderBy('nama_barang')->get();
        }

        $lokasiList = collect([]);

        return view('pages.pengerjaan', compact(
            'karyawanList',
            'barangList',
            'lokasiList',
            'subcon'
        ));
    }

    /**
     * Simpan pengerjaan baru (Logged In)
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'karyawan_id'      => 'required|exists:tb_karyawan,id',
            'barang_id'        => 'required|exists:tb_barang,id',
            'tanggal'          => 'required|date',
            'jumlah'           => 'required|integer|min:1',
            'jenis_pekerjaan'  => 'nullable',
            'keterangan'       => 'nullable|string',
        ];

        if ($user->is_admin) {
            $rules['lokasi_subcon_id'] = 'required|exists:tb_lokasi_subcon,id';
            $lokasiSubconId = $request->lokasi_subcon_id;
        } else {
            $subcon = $user->lokasiSubcon;
            if (!$subcon) {
                return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan lokasi subcon. Hubungi admin.');
            }
            $lokasiSubconId = $subcon->id;
        }

        $request->validate($rules);

        // Format jenis_pekerjaan dari checkbox array atau string
        $jenisPekerjaan = $request->input('jenis_pekerjaan');
        if (is_array($jenisPekerjaan)) {
            $jenisPekerjaan = implode(', ', array_filter($jenisPekerjaan));
        }

        try {
            Pengerjaan::create([
                'karyawan_id'      => $request->karyawan_id,
                'barang_id'        => $request->barang_id,
                'lokasi_subcon_id' => $lokasiSubconId,
                'jenis_pekerjaan'  => $jenisPekerjaan ?: null,
                'tanggal'          => $request->tanggal,
                'jumlah'           => $request->jumlah,
                'keterangan'       => $request->keterangan,
            ]);

            return redirect()->back()->with('success', 'Data pengerjaan barang berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('CreatePengerjaan error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pengerjaan barang.');
        }
    }

    /**
     * Halaman Laporan Subcon (dengan filter tanggal, karyawan, barang, dan lokasi)
     */
    public function laporan(Request $request)
    {
        $user = auth()->user();

        $defaultMulai = now()->startOfMonth()->toDateString();
        $defaultAkhir = now()->toDateString();

        $tanggalMulai     = $request->input('tanggal_mulai', $defaultMulai);
        $tanggalAkhir     = $request->input('tanggal_akhir', $defaultAkhir);
        $selectedKaryawan = $request->input('karyawan_id');
        $selectedBarang   = $request->input('barang_id');
        $selectedLokasi   = $request->input('lokasi_subcon_id');

        $isFiltered = $request->has('filter');

        $pengerjaan = collect([]);

        if ($isFiltered) {
            $query = DB::table('tb_pengerjaan as p')
                ->join('tb_karyawan as k', 'k.id', '=', 'p.karyawan_id')
                ->join('tb_barang as b', 'b.id', '=', 'p.barang_id')
                ->join('tb_lokasi_subcon as l', 'l.id', '=', 'p.lokasi_subcon_id')
                ->select(
                    'p.id',
                    'p.tanggal',
                    'p.jenis_pekerjaan',
                    'k.nama_karyawan',
                    'k.no_karyawan',
                    'b.kode_barang',
                    'b.nama_barang',
                    'b.satuan',
                    'l.nama_lokasi',
                    'p.jumlah',
                    'p.keterangan'
                );

            // Filter Tanggal
            if ($tanggalMulai) {
                $query->whereDate('p.tanggal', '>=', $tanggalMulai);
            }
            if ($tanggalAkhir) {
                $query->whereDate('p.tanggal', '<=', $tanggalAkhir);
            }

            // Filter Barang
            if ($selectedBarang) {
                $query->where('p.barang_id', $selectedBarang);
            }

            // Filter Karyawan
            if ($selectedKaryawan) {
                $query->where('p.karyawan_id', $selectedKaryawan);
            }

            // Role check & Filter Lokasi
            if ($user->is_admin) {
                if ($selectedLokasi) {
                    $query->where('p.lokasi_subcon_id', $selectedLokasi);
                }
            } else {
                $subcon = $user->lokasiSubcon;
                if ($subcon) {
                    // Akun subcon HANYA bisa melihat pengerjaan di subcon miliknya
                    $query->where('p.lokasi_subcon_id', $subcon->id);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            $pengerjaan = $query->orderBy('p.tanggal', 'desc')
                ->orderBy('p.id', 'desc')
                ->get();
        }

        // Data dropdown untuk filter
        if ($user->is_admin) {
            $karyawanList = Karyawan::where('is_active', true)->orderBy('nama_karyawan')->get();
            $barangList   = Barang::where('is_active', true)->orderBy('nama_barang')->get();
            $lokasiList   = LokasiSubcon::where('is_active', true)->orderBy('nama_lokasi')->get();
            $subcon       = null;
        } else {
            $subcon   = $user->lokasiSubcon;
            $subconId = $subcon?->id;

            $karyawanList = Karyawan::where('lokasi_subcon_id', $subconId)->where('is_active', true)->orderBy('nama_karyawan')->get();
            if ($karyawanList->isEmpty()) {
                $karyawanList = Karyawan::where('is_active', true)->orderBy('nama_karyawan')->get();
            }

            $barangList = collect([]);
            if ($subcon) {
                $barangList = $subcon->barang()->where('is_active', true)->orderBy('nama_barang')->get();
            }
            if ($barangList->isEmpty()) {
                $barangList = Barang::where('is_active', true)->orderBy('nama_barang')->get();
            }

            $lokasiList = collect([]);
        }

        $selectedKaryawanObj = $selectedKaryawan ? collect($karyawanList)->firstWhere('id', $selectedKaryawan) : null;
        $selectedBarangObj   = $selectedBarang ? $barangList->firstWhere('id', $selectedBarang) : null;
        $selectedLokasiObj   = ($user->is_admin && $selectedLokasi) ? $lokasiList->firstWhere('id', $selectedLokasi) : null;

        return view('pages.laporan-subcon', compact(
            'pengerjaan',
            'isFiltered',
            'karyawanList',
            'barangList',
            'lokasiList',
            'subcon',
            'tanggalMulai',
            'tanggalAkhir',
            'selectedKaryawan',
            'selectedBarang',
            'selectedLokasi',
            'selectedKaryawanObj',
            'selectedBarangObj',
            'selectedLokasiObj'
        ));
    }

    /**
     * Alias untuk riwayat agar kompatibel
     */
    public function riwayat(Request $request)
    {
        return $this->laporan($request);
    }

    /**
     * Hapus data pengerjaan
     */
    public function destroy($id)
    {
        $user       = auth()->user();
        $pengerjaan = Pengerjaan::findOrFail($id);

        // Akun Subcon hanya bisa menghapus data subcon miliknya
        if (!$user->is_admin) {
            if ($user->lokasiSubcon?->id !== $pengerjaan->lokasi_subcon_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data ini.',
                ], 403);
            }
        }

        $pengerjaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengerjaan barang berhasil dihapus',
        ]);
    }
}
