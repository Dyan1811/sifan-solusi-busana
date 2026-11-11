<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    protected $fillable = [
        'id_pesanan', 'jenis_pakaian', 'catatan',
        'tingkat_kesulitan', 'image_path', 'estimasi_waktu', 'status_pakaian'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}