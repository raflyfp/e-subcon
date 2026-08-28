<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiSubcon extends Model
{
    protected $table = 'tb_lokasi_subcon';

    protected $fillable = ['user_id', 'nama_lokasi', 'alamat', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'lokasi_subcon_id', 'id');
    }

    public function barang()
    {
        return $this->hasMany(Barang::class, 'lokasi_subcon_id', 'id');
    }

    public function pengerjaan()
    {
        return $this->hasMany(Pengerjaan::class, 'lokasi_subcon_id', 'id');
    }
}
