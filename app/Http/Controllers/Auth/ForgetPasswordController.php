<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        // Validasi format email
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid (contoh: nama@domain.com).',
        ]);

        // Cek apakah email terdaftar di database
        $userExists = User::where('email', $request->email)->exists();
        if (!$userExists) {
            return back()->withInput()->withErrors([
                'email' => 'Alamat email tidak terdaftar dalam sistem kami.'
            ]);
        }

        // Pengiriman link reset password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('status', 'Tautan pemulihan password telah dikirim ke email Anda.')
                    : back()->withInput()->withErrors(['email' => __($status)]);
    }
}