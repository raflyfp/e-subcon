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
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
        ];
    }

    //fungsi untuk generate remember token untuk kebutuhan remember me
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->remember_token)) {
                $user->remember_token = \Illuminate\Support\Str::random(60);
            }
        });
    }

    public function lokasiSubcon()
    {
        return $this->hasOne(LokasiSubcon::class, 'user_id', 'id');
    }
}
