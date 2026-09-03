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
        'jam_mulai',
        'jam_selesai',
        'durasi_menit',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'durasi_menit' => 'integer',
    ];

    /**
     * Format durasi menit ke bentuk teks yang mudah dibaca (misal: "1 Jam 30 Menit")
     */
    public function getDurasiTextAttribute(): string
    {
        if (is_null($this->durasi_menit) || $this->durasi_menit <= 0) {
            return '-';
        }

        $jam = intdiv($this->durasi_menit, 60);
        $menit = $this->durasi_menit % 60;

        if ($jam > 0 && $menit > 0) {
            return "{$jam} Jam {$menit} Menit";
        } elseif ($jam > 0) {
            return "{$jam} Jam";
        } else {
            return "{$menit} Menit";
        }
    }

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
