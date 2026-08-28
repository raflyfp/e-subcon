<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterBarangController extends Controller
{
    /**
     * Halaman Master Barang
     */
    public function index()
    {
        $barang = Barang::orderBy('is_active', 'desc')
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view('pages.barang', compact('barang'));
    }

    /**
     * Tambah barang baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:tb_barang,kode_barang',
            'nama_barang' => 'required|string',
        ]);

        try {
            Barang::create([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
            ]);

            return redirect()->back()->with('success', 'Barang berhasil ditambahkan');
        } catch (\Throwable $e) {
            Log::error('CreateBarang error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan barang.');
        }
    }

    /**
     * Update data barang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|unique:tb_barang,kode_barang,' . $id,
            'nama_barang' => 'required|string',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
        ]);

        return redirect()->back()->with('success', 'Data barang berhasil diupdate');
    }

    /**
     * Toggle status aktif/nonaktif barang
     */
    public function toggleStatus($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->is_active = !$barang->is_active;
        $barang->save();

        return response()->json([
            'success'   => true,
            'message'   => $barang->is_active ? 'Barang berhasil diaktifkan' : 'Barang berhasil dinonaktifkan',
            'is_active' => $barang->is_active,
        ]);
    }
}
