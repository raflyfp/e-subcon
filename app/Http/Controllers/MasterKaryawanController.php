<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
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
        $karyawan = DB::table('tb_karyawan as k')
            ->join('tb_user as u', 'u.id', '=', 'k.user_id')
            ->select(
                'k.id',
                'k.no_karyawan',
                'u.name as nama_karyawan',
                'k.telepon',
                'k.is_active'
            )
            ->orderBy('k.is_active', 'desc')
            ->orderBy('u.name', 'asc')
            ->get();

        return view('pages.karyawan', compact('karyawan'));
    }

    /**
     * Data AJAX untuk dropdown user yang belum terdaftar sebagai karyawan
     */
    public function getData()
    {
        return response()->json([
            'user' => User::select('id', 'username', 'name')
                ->where('is_admin', 0)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tb_karyawan')
                        ->whereColumn('tb_karyawan.user_id', 'tb_user.id');
                })
                ->get(),
        ]);
    }

    /**
     * Tambah karyawan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:tb_user,id',
            'no_karyawan'  => 'required|unique:tb_karyawan,no_karyawan',
            'telepon'      => 'nullable|string|max:20',
        ]);

        try {
            Karyawan::create([
                'user_id'      => $request->user_id,
                'no_karyawan'  => $request->no_karyawan,
                'telepon'      => $request->telepon,
            ]);

            return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan');
        } catch (\Throwable $e) {
            Log::error('CreateKaryawan error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan karyawan.');
        }
    }

    /**
     * Update data karyawan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'no_karyawan' => 'required|unique:tb_karyawan,no_karyawan,' . $id,
            'telepon'     => 'nullable|string|max:20',
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update([
            'no_karyawan' => $request->no_karyawan,
            'telepon'     => $request->telepon,
        ]);

        return redirect()->back()->with('success', 'Data karyawan berhasil diupdate');
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
