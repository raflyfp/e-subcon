<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    /**
     * Halaman Master User
     */
    public function index()
    {
        $user = DB::table('tb_user')
            ->select('id', 'username', 'name')
            ->where('is_admin', 0)
            ->orderBy('id', 'asc')
            ->get();

        return view('pages.user', compact('user'));
    }

    /**
     * Data AJAX untuk dropdown user yang belum terdaftar sebagai karyawan
     */
    public function getData()
    {
        return response()->json([
            'user' => User::select('tb_user.id', 'tb_user.username', 'tb_user.name')
                ->where('tb_user.is_admin', 0)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tb_karyawan')
                        ->whereColumn('tb_karyawan.user_id', 'tb_user.id');
                })
                ->get(),
        ]);
    }

    /**
     * Tambah user baru
     */
    public function CreateUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'username' => 'required|unique:tb_user,username',
            'password' => 'required|min:5',
        ]);

        try {
            DB::beginTransaction();

            User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'is_admin' => 0,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'User berhasil ditambahkan');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('CreateUser error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user. Silakan coba lagi.');
        }
    }
}
