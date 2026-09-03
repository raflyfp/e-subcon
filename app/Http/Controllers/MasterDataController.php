<?php

namespace App\Http\Controllers;

use App\Models\LokasiSubcon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    /**
     * Halaman Master User & Hak Akses
     */
    public function index()
    {
        $user = User::with('lokasiSubcon')
            ->orderBy('id', 'asc')
            ->get();

        $permissionGroups = User::PERMISSION_GROUPS;
        $permissionMatrix = User::PERMISSION_MATRIX;
        $hasSuperAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->exists();

        return view('pages.user', compact('user', 'permissionGroups', 'permissionMatrix', 'hasSuperAdmin'));
    }

    /**
     * Data AJAX untuk dropdown user
     */
    public function getData()
    {
        return response()->json([
            'user' => User::select('id', 'username', 'name')
                ->where('role', User::ROLE_SUBCON)
                ->orWhere('is_admin', 0)
                ->get(),
        ]);
    }

    /**
     * Tambah user baru beserta Role & Hak Akses
     */
    public function CreateUser(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:100|unique:tb_user,username',
            'password'    => 'required|min:5',
            'role'        => 'required|string',
            'permissions' => 'nullable|array',
        ]);

        $role = $request->input('role', User::ROLE_ADMIN_BIASA);
        $permissions = $request->input('permissions', []);

        // Batasi akun Super Admin hanya 1
        if ($role === User::ROLE_SUPER_ADMIN) {
            if (User::where('role', User::ROLE_SUPER_ADMIN)->exists()) {
                return redirect()->back()->withInput()->with('error', 'Akun Super Admin dibatasi hanya 1 akun. Silakan pilih role lain (Admin PPIC, Admin Biasa, atau User).');
            }
            $permissions = array_keys(User::getAllPermissions());
        }

        // Tentukan is_admin (role admin = 1, role user/subcon = 0)
        $isAdmin = in_array($role, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPIC, User::ROLE_ADMIN_BIASA], true) ? 1 : 0;

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'        => $request->name,
                'username'    => $request->username,
                'password'    => Hash::make($request->password),
                'role'        => $role,
                'permissions' => $permissions,
                'is_admin'    => $isAdmin,
                'is_active'   => true,
            ]);

            // Jika role adalah subcon, otomatis buatkan master lokasi subcon jika belum ada
            if ($role === User::ROLE_SUBCON) {
                LokasiSubcon::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama_lokasi' => $request->name,
                        'alamat'      => '-',
                        'is_active'   => true,
                    ]
                );
            }

            DB::commit();

            \App\Models\ActivityLog::record(
                'Master User',
                'CREATE',
                "Menambahkan user baru: {$user->name} ({$user->username}) dengan role {$user->role}"
            );

            return redirect()->back()->with('success', 'User dan Hak Akses berhasil ditambahkan.');
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

    /**
     * Update data User, Role, dan Hak Akses
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:100|unique:tb_user,username,' . $id,
            'password'    => 'nullable|min:5',
            'role'        => 'required|string',
            'permissions' => 'nullable|array',
        ]);

        $role = $request->input('role', $user->role);
        $permissions = $request->input('permissions', []);

        // Batasi akun Super Admin hanya 1
        if ($role === User::ROLE_SUPER_ADMIN) {
            $otherSuperAdminExists = User::where('role', User::ROLE_SUPER_ADMIN)
                ->where('id', '!=', $id)
                ->exists();

            if ($otherSuperAdminExists) {
                return redirect()->back()->withInput()->with('error', 'Akun Super Admin dibatasi hanya 1 akun. Silakan pilih role lain.');
            }
            $permissions = array_keys(User::getAllPermissions());
        }

        $isAdmin = in_array($role, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPIC, User::ROLE_ADMIN_BIASA], true) ? 1 : 0;

        try {
            DB::beginTransaction();

            $updateData = [
                'name'        => $request->name,
                'username'    => $request->username,
                'role'        => $role,
                'permissions' => $permissions,
                'is_admin'    => $isAdmin,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Jika role adalah subcon, pastikan data lokasi subcon tersinkron
            if ($role === User::ROLE_SUBCON) {
                $lokasi = LokasiSubcon::where('user_id', $user->id)->first();
                if ($lokasi) {
                    $lokasi->update(['nama_lokasi' => $request->name]);
                } else {
                    LokasiSubcon::create([
                        'user_id'     => $user->id,
                        'nama_lokasi' => $request->name,
                        'alamat'      => '-',
                        'is_active'   => true,
                    ]);
                }
            }

            DB::commit();

            \App\Models\ActivityLog::record(
                'Master User',
                'UPDATE',
                "Memperbarui user: {$user->name} ({$user->username}), role: {$user->role}"
            );

            return redirect()->back()->with('success', 'Data user dan hak akses berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('UpdateUser error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data user.');
        }
    }

    /**
     * Toggle status aktif/non-aktif user
     */
    public function toggleStatus($id)
    {
        $authUser = auth()->user();

        // Cegah user menonaktifkan akunnya sendiri yang sedang login
        if ($authUser->id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif digunakan.',
            ], 422);
        }

        $user = User::findOrFail($id);

        // Cegah penonaktifan Super Admin
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Super Admin tidak dapat dinonaktifkan.',
            ], 422);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        \App\Models\ActivityLog::record(
            'Master User',
            'TOGGLE_STATUS',
            "Mengubah status akun user {$user->name} ({$user->username}) menjadi " . ($user->is_active ? 'Aktif' : 'Nonaktif')
        );

        return response()->json([
            'success'   => true,
            'is_active' => $user->is_active,
            'message'   => "User {$user->name} berhasil {$statusText}.",
        ]);
    }
}
