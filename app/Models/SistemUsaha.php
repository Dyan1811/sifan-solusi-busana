<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemUsaha extends Model
{
    protected $table = 'sistem_usaha';
    protected $primaryKey = 'id_sistem';
    protected $fillable = ['id_user', 'nama_usaha', 'kapasitas_kerja', 'zona_waktu'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}