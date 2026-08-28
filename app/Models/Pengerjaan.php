<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengerjaan extends Model
{
    protected $table = 'tb_pengerjaan';

    protected $fillable = [
        'karyawan_id',
        'barang_id',
        'lokasi_subcon_id',
        'jenis_pekerjaan',
        'tanggal',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }

    public function lokasiSubcon()
    {
        return $this->belongsTo(LokasiSubcon::class, 'lokasi_subcon_id', 'id');
    }
}
