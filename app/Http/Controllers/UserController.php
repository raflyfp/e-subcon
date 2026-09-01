<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            if (auth()->user()->is_admin) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('pengerjaan.index');
        }

        return back()->with('error', 'Username atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function password()
    {
        return view('pages.password');
    }

    public function ChangePassword(Request $request)
    {
        $request->validate([
            'old_password'  => 'required',
            'new_password'  => 'required|min:5',
            'conf_password' => 'required|min:5',
        ], [
            'new_password.required'  => 'Password baru wajib diisi.',
            'new_password.min'       => 'Password baru harus memiliki minimal 5 karakter.',
            'conf_password.required' => 'Konfirmasi password wajib diisi.',
            'conf_password.min'      => 'Konfirmasi password harus memiliki minimal 5 karakter.',
        ]);

        if ($request->new_password !== $request->conf_password) {
            return back()->with('error', 'Password baru dan konfirmasi password tidak cocok.');
        }

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Password lama yang Anda masukkan tidak sesuai.');
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
