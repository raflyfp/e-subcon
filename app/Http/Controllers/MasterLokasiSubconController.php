<?php

namespace App\Http\Controllers;

use App\Models\LokasiSubcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterLokasiSubconController extends Controller
{
    /**
     * Halaman Master Lokasi Subcon
     */
    public function index()
    {
        $lokasi = LokasiSubcon::orderBy('is_active', 'desc')
            ->orderBy('nama_lokasi', 'asc')
            ->get();

        return view('pages.lokasi-subcon', compact('lokasi'));
    }

    /**
     * Tambah lokasi subcon baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string',
            'alamat'      => 'nullable|string',
        ]);

        try {
            LokasiSubcon::create([
                'nama_lokasi' => $request->nama_lokasi,
                'alamat'      => $request->alamat,
            ]);

            return redirect()->back()->with('success', 'Lokasi subcon berhasil ditambahkan');
        } catch (\Throwable $e) {
            Log::error('CreateLokasiSubcon error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan lokasi subcon.');
        }
    }

    /**
     * Update data lokasi subcon
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lokasi' => 'required|string',
            'alamat'      => 'nullable|string',
        ]);

        $lokasi = LokasiSubcon::findOrFail($id);
        $lokasi->update([
            'nama_lokasi' => $request->nama_lokasi,
            'alamat'      => $request->alamat,
        ]);

        return redirect()->back()->with('success', 'Data lokasi subcon berhasil diupdate');
    }

    /**
     * Toggle status aktif/nonaktif lokasi subcon
     */
    public function toggleStatus($id)
    {
        $lokasi = LokasiSubcon::findOrFail($id);
        $lokasi->is_active = !$lokasi->is_active;
        $lokasi->save();

        return response()->json([
            'success'   => true,
            'message'   => $lokasi->is_active ? 'Lokasi berhasil diaktifkan' : 'Lokasi berhasil dinonaktifkan',
            'is_active' => $lokasi->is_active,
        ]);
    }
}
