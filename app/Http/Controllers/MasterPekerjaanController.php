<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterPekerjaanController extends Controller
{
    /**
     * Halaman Master Pekerjaan
     */
    public function index()
    {
        $pekerjaan = Pekerjaan::orderBy('is_active', 'desc')
            ->orderBy('nama_pekerjaan', 'asc')
            ->get();

        return view('pages.pekerjaan', compact('pekerjaan'));
    }

    /**
     * Tambah pekerjaan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pekerjaan' => 'required|string|max:100|unique:tb_pekerjaan,nama_pekerjaan',
            'keterangan'     => 'nullable|string',
        ]);

        try {
            Pekerjaan::create([
                'nama_pekerjaan' => trim($request->nama_pekerjaan),
                'keterangan'     => $request->keterangan ? trim($request->keterangan) : null,
                'is_active'      => true,
            ]);

            return redirect()->back()->with('success', 'Master pekerjaan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('CreatePekerjaan error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data pekerjaan: ' . $e->getMessage());
        }
    }

    /**
     * Update data pekerjaan
     */
    public function update(Request $request, $id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);

        $request->validate([
            'nama_pekerjaan' => 'required|string|max:100|unique:tb_pekerjaan,nama_pekerjaan,' . $id,
            'keterangan'     => 'nullable|string',
        ]);

        try {
            $pekerjaan->update([
                'nama_pekerjaan' => trim($request->nama_pekerjaan),
                'keterangan'     => $request->keterangan ? trim($request->keterangan) : null,
            ]);

            return redirect()->back()->with('success', 'Data pekerjaan berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('UpdatePekerjaan error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pekerjaan.');
        }
    }

    /**
     * Toggle status aktif/nonaktif pekerjaan
     */
    public function toggleStatus($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $pekerjaan->is_active = !$pekerjaan->is_active;
        $pekerjaan->save();

        return response()->json([
            'success'   => true,
            'message'   => $pekerjaan->is_active ? 'Pekerjaan berhasil diaktifkan' : 'Pekerjaan berhasil dinonaktifkan',
            'is_active' => $pekerjaan->is_active,
        ]);
    }
}
