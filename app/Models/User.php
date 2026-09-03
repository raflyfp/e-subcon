<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tb_user';

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN_PPIC  = 'admin_ppic';
    public const ROLE_ADMIN_BIASA = 'admin_biasa';
    public const ROLE_USER        = 'user';
    public const ROLE_SUBCON      = 'subcon';

    public const AVAILABLE_PERMISSIONS = [
        'dashboard'            => 'Dashboard',
        'master_user'          => 'Master User',
        'master_karyawan'      => 'Master Karyawan',
        'master_barang'        => 'Master Barang',
        'master_pekerjaan'     => 'Master Pekerjaan',
        'master_lokasi_subcon' => 'Master Lokasi Subcon',
        'formulir_pengerjaan'  => 'Formulir Pengerjaan',
        'laporan_subcon'       => 'Laporan Subcon',
    ];

    protected $fillable = [
        'name',
        'username',
        'password',
        'is_admin',
        'role',
        'permissions',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_admin'    => 'boolean',
            'is_active'   => 'boolean',
            'permissions' => 'array',
        ];
    }

    /**
     * Memeriksa apakah user memiliki hak akses ke suatu menu/halaman
     */
    public function canAccess(string $permission): bool
    {
        // Jika akun nonaktif, tolak akses
        if (!$this->is_active) {
            return false;
        }

        // Super Admin memiliki akses penuh ke seluruh menu
        if ($this->role === self::ROLE_SUPER_ADMIN || ($this->is_admin && empty($this->permissions))) {
            return true;
        }

        // Cek array permissions yang tersimpan
        if (is_array($this->permissions)) {
            return in_array($permission, $this->permissions, true);
        }

        return false;
    }

    /**
     * Label teks nama Role yang mudah dibaca
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN_PPIC  => 'Admin PPIC',
            self::ROLE_ADMIN_BIASA => 'Admin Biasa',
            self::ROLE_USER        => 'User',
            self::ROLE_SUBCON      => 'Subcon',
            default                => ucfirst(str_replace('_', ' ', $this->role ?? 'User')),
        };
    }

    /**
     * Badge CSS class untuk role
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'bg-danger-subtle text-danger border-danger',
            self::ROLE_ADMIN_PPIC  => 'bg-purple-subtle text-primary border-primary',
            self::ROLE_ADMIN_BIASA => 'bg-info-subtle text-info border-info',
            self::ROLE_SUBCON      => 'bg-warning-subtle text-warning border-warning',
            default                => 'bg-secondary-subtle text-secondary border-secondary',
        };
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
