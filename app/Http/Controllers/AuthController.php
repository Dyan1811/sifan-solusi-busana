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

    // Menampilkan Halaman Register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses Pendaftaran Akun Baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap / nama usaha wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('kanban.index');
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