<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'tb_barang';

    protected $fillable = [
        'lokasi_subcon_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lokasiSubcon()
    {
        return $this->belongsTo(LokasiSubcon::class, 'lokasi_subcon_id', 'id');
    }

    public function pengerjaan()
    {
        return $this->hasMany(Pengerjaan::class, 'barang_id', 'id');
    }
}
