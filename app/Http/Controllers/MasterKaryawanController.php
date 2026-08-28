<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                'k.user_id',
                'k.no_karyawan',
                'u.name as nama_karyawan',
                'u.username',
                'k.telepon',
                'k.is_active'
            )
            ->orderBy('k.is_active', 'desc')
            ->orderBy('u.name', 'asc')
            ->get();

        return view('pages.karyawan', compact('karyawan'));
    }

    /**
     * Data AJAX untuk dropdown user (jika diperlukan)
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
     * Tambah karyawan baru (Otomatis membuat user akun untuk karyawan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_karyawan'   => 'required|string|max:50|unique:tb_karyawan,no_karyawan',
            'telepon'       => 'nullable|string|max:20',
            'username'      => 'nullable|string|max:100|unique:tb_user,username',
            'password'      => 'nullable|string|min:4',
        ]);

        DB::beginTransaction();
        try {
            // Tentukan username & password (default jika kosong)
            $username = $request->filled('username')
                ? $request->username
                : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->no_karyawan));

            // Pastikan username unik bila default
            if (User::where('username', $username)->exists()) {
                $username = $username . '_' . rand(100, 999);
            }

            $password = $request->filled('password') ? $request->password : '12345678';

            // 1. Buat User baru
            $user = User::create([
                'name'      => $request->nama_karyawan,
                'username'  => $username,
                'password'  => Hash::make($password),
                'is_admin'  => 0,
            ]);

            // 2. Buat Karyawan terkait
            Karyawan::create([
                'user_id'     => $user->id,
                'no_karyawan' => $request->no_karyawan,
                'telepon'     => $request->telepon,
                'is_active'   => true,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Karyawan dan akun login berhasil ditambahkan (Username: ' . $username . ')');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CreateKaryawan error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Update data karyawan (Bisa edit nama karyawan, no. karyawan, telepon, dan password)
     */
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $user = User::findOrFail($karyawan->user_id);

        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_karyawan'   => 'required|string|max:50|unique:tb_karyawan,no_karyawan,' . $id,
            'telepon'       => 'nullable|string|max:20',
            'username'      => 'nullable|string|max:100|unique:tb_user,username,' . $user->id,
            'password'      => 'nullable|string|min:4',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update User (Nama, Username, Password bila diisi)
            $userData = [
                'name' => $request->nama_karyawan,
            ];

            if ($request->filled('username')) {
                $userData['username'] = $request->username;
            }

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // 2. Update Karyawan
            $karyawan->update([
                'no_karyawan' => $request->no_karyawan,
                'telepon'     => $request->telepon,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
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
