<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');
            $table->string('jenis_pakaian');
            $table->text('catatan')->nullable();
            $table->enum('tingkat_kesulitan', ['ringan', 'sedang', 'rumit']); // 15m, 30m, 60m
            $table->string('image_path')->nullable();
            $table->integer('estimasi_waktu');
            $table->enum('status_pakaian', ['belum', 'selesai'])->default('belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};