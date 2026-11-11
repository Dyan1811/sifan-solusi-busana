<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HistoryController;

// Autentikasi Pemilik Usaha
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
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