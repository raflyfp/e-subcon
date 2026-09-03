<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'tb_pekerjaan';

    protected $fillable = [
        'nama_pekerjaan',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'pekerjaan_id', 'id');
    }
}
