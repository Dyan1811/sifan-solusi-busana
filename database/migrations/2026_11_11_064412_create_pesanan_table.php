<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->string('nomor_pesanan')->unique();
            $table->foreignId('id_user')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->onDelete('cascade');
            $table->date('tanggal_pesanan');
            $table->dateTime('tenggat_pengambilan');
            $table->enum('status_pesanan', ['antrean', 'diproses', 'selesai', 'siap_diambil', 'sudah_diambil'])->default('antrean');
            $table->integer('jumlah_pakaian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};