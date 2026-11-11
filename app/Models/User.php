<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'nama_usaha', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    public function sistemUsaha()
    {
        return $this->hasOne(SistemUsaha::class, 'id_user', 'id');
    }

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'id_user', 'id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_user', 'id');
    }
}