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
}
