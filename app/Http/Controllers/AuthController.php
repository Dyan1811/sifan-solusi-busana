<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SistemUsaha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('kanban.index'));
        }

        return back()->withErrors(['email' => 'Kredensial tidak cocok dengan data kami.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validasi input data registrasi
        $request->validate([
            'name' => 'required|string|max:255',
            'nama_usaha' => 'required|string|max:255',
            // Format email wajib memenuhi rfc & regex domain (contoh: user@domain.com)
            'email' => ['required', 'string', 'email:rfc', 'regex:/^.+@.+\..+$/i', 'max:255', 'unique:users'],
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nama_usaha.required' => 'Nama usaha wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format penulisan email tidak valid.',
            'email.regex' => 'Penulisan email harus menggunakan domain yang sah (contoh: nama@domain.com atau nama@domain.co.id).',
            'email.unique' => 'Alamat email ini sudah terdaftar dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'nama_usaha' => $request->nama_usaha,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
        ]);

        SistemUsaha::create([
            'id_user' => $user->id,
            'nama_usaha' => $request->nama_usaha,
            'kapasitas_kerja' => 480,
            'zona_waktu' => 'Asia/Pontianak'
        ]);

        Auth::login($user);

        return redirect()->route('kanban.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}