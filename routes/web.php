<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HistoryController;

// --------------------------------------------------------------------------
// RUTE UNTUK PENGGUNA YANG BELUM LOGIN (GUEST)
// --------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    // Autentikasi Login & Register
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Tampilan Form Lupa Password
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // Proses Kirim Email Pemulihan Password dengan Validasi Lengkap
    Route::post('forgot-password', function (Request $request) {
        // Otomatis buat tabel password_reset_tokens jika belum ada di Railway
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 1. Validasi Format Email
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid (contoh: nama@domain.com).',
        ]);

        // 2. Cek Apakah Email Terdaftar di Database
        $userExists = User::where('email', $request->email)->exists();
        if (!$userExists) {
            return back()->withInput()->withErrors([
                'email' => 'Alamat email ini belum terdaftar dalam sistem kami.'
            ]);
        }

        // 3. Kirim Link Reset Password
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Tautan pemulihan password telah dikirim ke email Anda.')
            : back()->withInput()->withErrors(['email' => __($status)]);
    })->name('password.email');

    // Halaman Form Input Password Baru (saat link reset diklik)
    Route::get('/reset-password/{token}', function (string $token, Request $request) {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    })->name('password.reset');

    // Proses Simpan Password Baru ke Database
    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Kata sandi berhasil diperbarui! Silakan login.')
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    })->name('password.update');
});

// --------------------------------------------------------------------------
// RUTE UNTUK PENGGUNA YANG SUDAH LOGIN (AUTH)
// --------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Kanban Antrean
    Route::get('/', [KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/orders/{id}/status', [KanbanController::class, 'updateOrderStatus'])->name('orders.updateStatus');
    Route::post('/items/{id}/status', [KanbanController::class, 'updateItemStatus'])->name('items.updateStatus');
    Route::post('/orders/{id}/send-wa', [KanbanController::class, 'sendWaAndSetReady'])->name('orders.sendWa');

    // Tambah Pesanan & Cek Kapasitas Kuota Harian
    Route::get('/orders/check-capacity', [OrderController::class, 'checkCapacity'])->name('orders.checkCapacity');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Riwayat Orderan
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::post('/orders/{id}/archive', [HistoryController::class, 'archive'])->name('orders.archive');
});