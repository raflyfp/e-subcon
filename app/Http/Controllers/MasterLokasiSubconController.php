<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LokasiSubcon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MasterLokasiSubconController extends Controller
{
    /**
     * Halaman Master Lokasi Subcon
     */
    public function index()
    {
        $lokasi = LokasiSubcon::with(['user', 'barang'])
            ->orderBy('is_active', 'desc')
            ->orderBy('nama_lokasi', 'asc')
            ->get();

        return view('pages.lokasi-subcon', compact('lokasi'));
    }

    /**
     * Tambah lokasi subcon baru beserta Akun Login
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat'      => 'nullable|string',
            'username'    => 'required|string|max:100|unique:tb_user,username',
            'password'    => 'required|string|min:4',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat User Akun Subcon
            $user = User::create([
                'name'      => $request->nama_lokasi,
                'username'  => $request->username,
                'password'  => Hash::make($request->password),
                'is_admin'  => 0,
            ]);

            // 2. Buat Lokasi Subcon
            LokasiSubcon::create([
                'user_id'     => $user->id,
                'nama_lokasi' => $request->nama_lokasi,
                'alamat'      => $request->alamat,
                'is_active'   => true,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Lokasi subcon dan akun login berhasil ditambahkan (Username: ' . $request->username . ')');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CreateLokasiSubcon error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan lokasi subcon: ' . $e->getMessage());
        }
    }

    /**
     * Update data lokasi subcon dan akun login
     */
    public function update(Request $request, $id)
    {
        $lokasi = LokasiSubcon::findOrFail($id);
        $userId = $lokasi->user_id;

        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat'      => 'nullable|string',
            'username'    => 'nullable|string|max:100|unique:tb_user,username,' . ($userId ?? 'NULL'),
            'password'    => 'nullable|string|min:4',
        ]);

        DB::beginTransaction();
        try {
            // 1. Kelola User Akun Subcon
            if ($lokasi->user_id) {
                $user = User::find($lokasi->user_id);
                if ($user) {
                    $userData = ['name' => $request->nama_lokasi];
                    if ($request->filled('username')) {
                        $userData['username'] = $request->username;
                    }
                    if ($request->filled('password')) {
                        $userData['password'] = Hash::make($request->password);
                    }
                    $user->update($userData);
                }
            } else if ($request->filled('username')) {
                $password = $request->filled('password') ? $request->password : '12345';
                $user = User::create([
                    'name'     => $request->nama_lokasi,
                    'username' => $request->username,
                    'password' => Hash::make($password),
                    'is_admin' => 0,
                ]);
                $lokasi->user_id = $user->id;
            }

            // 2. Update Lokasi Subcon
            $lokasi->update([
                'nama_lokasi' => $request->nama_lokasi,
                'alamat'      => $request->alamat,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data lokasi subcon berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('UpdateLokasiSubcon error', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data lokasi subcon.');
        }
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
