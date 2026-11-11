<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatusPesanan extends Model
{
    protected $table = 'riwayat_status_pesanan';
    protected $primaryKey = 'id_riwayat';
    public $timestamps = false;
    protected $fillable = ['id_pesanan', 'status_sebelumnya', 'status_baru', 'diubah_oleh', 'waktu_diubah'];

    protected $casts = [
        'waktu_diubah' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'diubah_oleh', 'id');
    }
}