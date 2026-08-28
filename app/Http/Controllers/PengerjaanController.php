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
            // Admin: lihat ringkasan seluruh data
            $pengerjaanHariIni  = Pengerjaan::where('tanggal', $today)->sum('jumlah');
            $pengerjaanBulanIni = Pengerjaan::where('tanggal', '>=', $monthStart)->sum('jumlah');
            $totalKaryawan      = Karyawan::where('is_active', true)->count();
            $totalBarang         = Barang::where('is_active', true)->count();

            return view('pages.dashboard', compact(
                'pengerjaanHariIni',
                'pengerjaanBulanIni',
                'totalKaryawan',
                'totalBarang'
            ));
        }

        // Karyawan: lihat ringkasan milik sendiri
        $karyawan = $user->karyawan;
        $pengerjaanHariIni  = 0;
        $pengerjaanBulanIni = 0;

        if ($karyawan) {
            $pengerjaanHariIni  = $karyawan->pengerjaan()->where('tanggal', $today)->sum('jumlah');
            $pengerjaanBulanIni = $karyawan->pengerjaan()->where('tanggal', '>=', $monthStart)->sum('jumlah');
        }

        return view('pages.dashboard', compact(
            'pengerjaanHariIni',
            'pengerjaanBulanIni'
        ));
    }

    /**
     * Halaman daftar pengerjaan
     */
    public function index()
    {
        $user = auth()->user();

        $query = DB::table('tb_pengerjaan as p')
            ->join('tb_karyawan as k', 'k.id', '=', 'p.karyawan_id')
            ->join('tb_user as u', 'u.id', '=', 'k.user_id')
            ->join('tb_barang as b', 'b.id', '=', 'p.barang_id')
            ->join('tb_lokasi_subcon as l', 'l.id', '=', 'p.lokasi_subcon_id')
            ->select(
                'p.id',
                'p.tanggal',
                'u.name as nama_karyawan',
                'k.no_karyawan',
                'b.kode_barang',
                'b.nama_barang',
                'l.nama_lokasi',
                'p.jumlah',
                'p.keterangan'
            );

        // Karyawan hanya melihat data milik sendiri
        if (!$user->is_admin) {
            $karyawan = $user->karyawan;
            if ($karyawan) {
                $query->where('p.karyawan_id', $karyawan->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $pengerjaan = $query->orderBy('p.tanggal', 'desc')
            ->orderBy('p.id', 'desc')
            ->get();

        // Data untuk dropdown form input
        $karyawanList = [];
        $barangList   = Barang::where('is_active', true)->orderBy('nama_barang')->get();
        $lokasiList   = LokasiSubcon::where('is_active', true)->orderBy('nama_lokasi')->get();

        if ($user->is_admin) {
            $karyawanList = DB::table('tb_karyawan as k')
                ->join('tb_user as u', 'u.id', '=', 'k.user_id')
                ->where('k.is_active', true)
                ->select('k.id', 'u.name as nama_karyawan', 'k.no_karyawan')
                ->orderBy('u.name')
                ->get();
        }

        return view('pages.pengerjaan', compact(
            'pengerjaan',
            'karyawanList',
            'barangList',
            'lokasiList'
        ));
    }

    /**
     * Simpan pengerjaan baru
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'barang_id'        => 'required|exists:tb_barang,id',
            'lokasi_subcon_id' => 'required|exists:tb_lokasi_subcon,id',
            'tanggal'          => 'required|date',
            'jumlah'           => 'required|integer|min:1',
            'keterangan'       => 'nullable|string',
        ];

        // Admin harus memilih karyawan, karyawan biasa otomatis dari session
        if ($user->is_admin) {
            $rules['karyawan_id'] = 'required|exists:tb_karyawan,id';
        }

        $request->validate($rules);

        $karyawanId = $user->is_admin
            ? $request->karyawan_id
            : $user->karyawan?->id;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan. Hubungi admin.');
        }

        try {
            Pengerjaan::create([
                'karyawan_id'      => $karyawanId,
                'barang_id'        => $request->barang_id,
                'lokasi_subcon_id' => $request->lokasi_subcon_id,
                'tanggal'          => $request->tanggal,
                'jumlah'           => $request->jumlah,
                'keterangan'       => $request->keterangan,
            ]);

            return redirect()->back()->with('success', 'Pengerjaan berhasil ditambahkan');
        } catch (\Throwable $e) {
            Log::error('CreatePengerjaan error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pengerjaan.');
        }
    }

    /**
     * Hapus data pengerjaan
     */
    public function destroy($id)
    {
        $user       = auth()->user();
        $pengerjaan = Pengerjaan::findOrFail($id);

        // Karyawan hanya bisa menghapus data milik sendiri
        if (!$user->is_admin) {
            if ($user->karyawan?->id !== $pengerjaan->karyawan_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data ini.',
                ], 403);
            }
        }

        $pengerjaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengerjaan berhasil dihapus',
        ]);
    }
}
