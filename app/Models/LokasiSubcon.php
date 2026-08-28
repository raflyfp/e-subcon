<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiSubcon extends Model
{
    protected $table = 'tb_lokasi_subcon';

    protected $fillable = ['nama_lokasi', 'alamat', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pengerjaan()
    {
        return $this->hasMany(Pengerjaan::class, 'lokasi_subcon_id', 'id');
    }
}
