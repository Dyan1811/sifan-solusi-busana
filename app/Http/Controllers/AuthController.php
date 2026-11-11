<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan Halaman Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login dengan Validasi Lengkap
    public function login(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid (contoh: nama@domain.com).',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // 2. Cek Apakah Email Terdaftar di Database
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Alamat email belum terdaftar dalam sistem.'
            ]);
        }

        // 3. Cek Apakah Password Sesuai
        if (!Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))->withErrors([
                'password' => 'Kata sandi yang Anda masukkan salah.'
            ]);
        }

        // 4. Jika Cocok, Melakukan Login & Regenerasi Session
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('kanban.index'));
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}