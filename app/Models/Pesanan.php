<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    protected $fillable = [
        'nomor_pesanan', 'id_user', 'id_pelanggan',
        'tanggal_pesanan', 'tenggat_pengambilan',
        'status_pesanan', 'jumlah_pakaian'
    ];

    protected $casts = [
        'tanggal_pesanan' => 'date',
        'tenggat_pengambilan' => 'datetime',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatusPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function getTotalEstimasiAttribute()
    {
        return $this->details->sum('estimasi_waktu');
    }
}