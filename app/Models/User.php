<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tb_user';

    protected $fillable = [
        'name',
        'username',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
        ];
    }

    public function lokasiSubcon()
    {
        return $this->hasOne(LokasiSubcon::class, 'user_id', 'id');
    }
}
