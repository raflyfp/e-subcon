<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'tb_karyawan';

    protected $fillable = [
        'nama_karyawan',
        'lokasi_subcon_id',
        'no_karyawan',
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
        return $this->hasMany(Pengerjaan::class, 'karyawan_id', 'id');
    }
}
