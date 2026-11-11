<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sistem_usaha', function (Blueprint $table) {
            $table->id('id_sistem');
            $table->foreignId('id_user')->constrained('users', 'id')->onDelete('cascade');
            $table->string('nama_usaha');
            $table->integer('kapasitas_kerja')->default(480); // Maksimal 8 jam = 480 menit
            $table->string('zona_waktu')->default('Asia/Pontianak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sistem_usaha');
    }
};