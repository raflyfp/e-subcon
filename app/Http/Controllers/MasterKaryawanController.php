<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\LokasiSubcon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterKaryawanController extends Controller
{
    /**
     * Halaman Master Karyawan
     */
    public function index()
    {
        $karyawan = Karyawan::with('lokasiSubcon')
            ->orderBy('is_active', 'desc')
            ->orderBy('nama_karyawan', 'asc')
            ->get();

        $subconList = LokasiSubcon::where('is_active', true)->orderBy('nama_lokasi')->get();

        return view('pages.karyawan', compact('karyawan', 'subconList'));
    }

    /**
     * Tambah karyawan baru (terhubung ke Lokasi Subcon)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan'    => 'required|string|max:255',
            'no_karyawan'      => 'required|string|max:50|unique:tb_karyawan,no_karyawan',
            'lokasi_subcon_id' => 'required|exists:tb_lokasi_subcon,id',
            'telepon'          => 'nullable|string|max:20',
        ]);

        try {
            Karyawan::create([
                'nama_karyawan'    => $request->nama_karyawan,
                'no_karyawan'      => $request->no_karyawan,
                'lokasi_subcon_id' => $request->lokasi_subcon_id,
                'telepon'          => $request->telepon,
                'is_active'        => true,
            ]);

            return redirect()->back()->with('success', 'Data karyawan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('CreateKaryawan error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Update data karyawan
     */
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nama_karyawan'    => 'required|string|max:255',
            'no_karyawan'      => 'required|string|max:50|unique:tb_karyawan,no_karyawan,' . $id,
            'lokasi_subcon_id' => 'required|exists:tb_lokasi_subcon,id',
            'telepon'          => 'nullable|string|max:20',
        ]);

        try {
            $karyawan->update([
                'nama_karyawan'    => $request->nama_karyawan,
                'no_karyawan'      => $request->no_karyawan,
                'lokasi_subcon_id' => $request->lokasi_subcon_id,
                'telepon'          => $request->telepon,
            ]);

            return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui');
        } catch (\Throwable $e) {
            Log::error('UpdateKaryawan error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data karyawan.');
        }
    }

    /**
     * Toggle status aktif/nonaktif karyawan
     */
    public function toggleStatus($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->is_active = !$karyawan->is_active;
        $karyawan->save();

        return response()->json([
            'success'   => true,
            'message'   => $karyawan->is_active ? 'Karyawan berhasil diaktifkan' : 'Karyawan berhasil dinonaktifkan',
            'is_active' => $karyawan->is_active,
        ]);
    }
}
