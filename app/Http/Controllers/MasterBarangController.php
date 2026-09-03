<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LokasiSubcon;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterBarangController extends Controller
{
    /**
     * Halaman Master Barang
     */
    public function index()
    {
        $barang = Barang::with(['lokasiSubcon', 'pekerjaan'])
            ->orderBy('is_active', 'desc')
            ->orderBy('nama_barang', 'asc')
            ->get();

        $subconList = LokasiSubcon::where('is_active', true)->orderBy('nama_lokasi')->get();
        $pekerjaanList = Pekerjaan::where('is_active', true)->orderBy('nama_pekerjaan')->get();

        return view('pages.barang', compact('barang', 'subconList', 'pekerjaanList'));
    }

    /**
     * Tambah barang baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'      => 'required|unique:tb_barang,kode_barang',
            'nama_barang'      => 'required|string|max:255',
            'satuan'           => 'required|string|max:50',
            'lokasi_subcon_id' => 'nullable|exists:tb_lokasi_subcon,id',
            'pekerjaan_id'     => 'nullable|exists:tb_pekerjaan,id',
        ]);

        try {
            $pekerjaan = $request->pekerjaan_id ? Pekerjaan::find($request->pekerjaan_id) : null;

            Barang::create([
                'kode_barang'      => $request->kode_barang,
                'nama_barang'      => $request->nama_barang,
                'satuan'           => strtoupper(trim($request->satuan)),
                'lokasi_subcon_id' => $request->lokasi_subcon_id ?: null,
                'pekerjaan_id'     => $pekerjaan?->id ?: null,
                'jenis_pekerjaan'  => $pekerjaan?->nama_pekerjaan ?: null,
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
            'kode_barang'      => 'required|unique:tb_barang,kode_barang,' . $id,
            'nama_barang'      => 'required|string|max:255',
            'satuan'           => 'required|string|max:50',
            'lokasi_subcon_id' => 'nullable|exists:tb_lokasi_subcon,id',
            'pekerjaan_id'     => 'nullable|exists:tb_pekerjaan,id',
        ]);

        $barang = Barang::findOrFail($id);
        $pekerjaan = $request->pekerjaan_id ? Pekerjaan::find($request->pekerjaan_id) : null;

        $barang->update([
            'kode_barang'      => $request->kode_barang,
            'nama_barang'      => $request->nama_barang,
            'satuan'           => strtoupper(trim($request->satuan)),
            'lokasi_subcon_id' => $request->lokasi_subcon_id ?: null,
            'pekerjaan_id'     => $pekerjaan?->id ?: null,
            'jenis_pekerjaan'  => $pekerjaan?->nama_pekerjaan ?: null,
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
