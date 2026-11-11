<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status_pesanan', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->foreignId('diubah_oleh')->constrained('users', 'id')->onDelete('cascade');
            $table->timestamp('waktu_diubah')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_pesanan');
    }
};