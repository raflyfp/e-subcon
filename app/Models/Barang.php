<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'tb_barang';

    protected $fillable = ['kode_barang', 'nama_barang', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pengerjaan()
    {
        return $this->hasMany(Pengerjaan::class, 'barang_id', 'id');
    }

    public function lokasiSubcon()
    {
        return $this->belongsToMany(LokasiSubcon::class, 'tb_lokasi_subcon_barang', 'barang_id', 'lokasi_subcon_id')->withTimestamps();
    }
}
