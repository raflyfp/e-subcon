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

    public const PERMISSION_GROUPS = [
        'Dashboard' => [
            'dashboard.view' => 'Lihat Dashboard',
        ],
        'Master User' => [
            'master_user.view'   => 'Lihat Menu & Data',
            'master_user.create' => 'Tambah User',
            'master_user.edit'   => 'Edit User & Hak Akses',
            'master_user.toggle' => 'Nonaktifkan / Aktifkan User',
        ],
        'Master Karyawan' => [
            'master_karyawan.view'   => 'Lihat Menu & Data',
            'master_karyawan.create' => 'Tambah Karyawan',
            'master_karyawan.edit'   => 'Edit Karyawan',
            'master_karyawan.toggle' => 'Nonaktifkan / Aktifkan Karyawan',
        ],
        'Master Barang' => [
            'master_barang.view'   => 'Lihat Menu & Data',
            'master_barang.create' => 'Tambah Barang',
            'master_barang.edit'   => 'Edit Barang',
            'master_barang.toggle' => 'Nonaktifkan / Aktifkan Barang',
        ],
        'Master Pekerjaan' => [
            'master_pekerjaan.view'   => 'Lihat Menu & Data',
            'master_pekerjaan.create' => 'Tambah Pekerjaan',
            'master_pekerjaan.edit'   => 'Edit Pekerjaan',
            'master_pekerjaan.toggle' => 'Nonaktifkan / Aktifkan Pekerjaan',
        ],
        'Master Lokasi Subcon' => [
            'master_lokasi_subcon.view'   => 'Lihat Menu & Data',
            'master_lokasi_subcon.create' => 'Tambah Lokasi Subcon',
            'master_lokasi_subcon.edit'   => 'Edit Lokasi Subcon',
            'master_lokasi_subcon.toggle' => 'Nonaktifkan / Aktifkan Lokasi Subcon',
        ],
        'Formulir Pengerjaan' => [
            'formulir_pengerjaan.view' => 'Buka Form & Input Pengerjaan',
        ],
        'Laporan Subcon' => [
            'laporan_subcon.view' => 'Lihat & Export Laporan',
        ],
        'Log Report' => [
            'log_report.view' => 'Lihat Log Report',
        ],
    ];

    public const PERMISSION_MATRIX = [
        'Dashboard' => [
            [
                'name'   => 'Dashboard Monitoring',
                'view'   => 'dashboard.view',
                'create' => null,
                'edit'   => null,
                'delete' => null,
            ],
        ],
        'Master Data' => [
            [
                'name'   => 'User & Hak Akses',
                'view'   => 'master_user.view',
                'create' => 'master_user.create',
                'edit'   => 'master_user.edit',
                'delete' => 'master_user.toggle',
            ],
            [
                'name'   => 'Karyawan',
                'view'   => 'master_karyawan.view',
                'create' => 'master_karyawan.create',
                'edit'   => 'master_karyawan.edit',
                'delete' => 'master_karyawan.toggle',
            ],
            [
                'name'   => 'Barang',
                'view'   => 'master_barang.view',
                'create' => 'master_barang.create',
                'edit'   => 'master_barang.edit',
                'delete' => 'master_barang.toggle',
            ],
            [
                'name'   => 'Pekerjaan',
                'view'   => 'master_pekerjaan.view',
                'create' => 'master_pekerjaan.create',
                'edit'   => 'master_pekerjaan.edit',
                'delete' => 'master_pekerjaan.toggle',
            ],
            [
                'name'   => 'Lokasi Subcon',
                'view'   => 'master_lokasi_subcon.view',
                'create' => 'master_lokasi_subcon.create',
                'edit'   => 'master_lokasi_subcon.edit',
                'delete' => 'master_lokasi_subcon.toggle',
            ],
        ],
        'Transaksi' => [
            [
                'name'   => 'Formulir Pengerjaan',
                'view'   => 'formulir_pengerjaan.view',
                'create' => null,
                'edit'   => null,
                'delete' => null,
            ],
        ],
        'Report' => [
            [
                'name'   => 'Laporan Pengerjaan Subcon',
                'view'   => 'laporan_subcon.view',
                'create' => null,
                'edit'   => null,
                'delete' => null,
            ],
            [
                'name'   => 'Log Report',
                'view'   => 'log_report.view',
                'create' => null,
                'edit'   => null,
                'delete' => null,
            ],
        ],
    ];

    public const AVAILABLE_PERMISSIONS = [
        'dashboard'                  => 'Dashboard',
        'dashboard.view'             => 'Lihat Dashboard',
        'master_user'                => 'Master User',
        'master_user.view'           => 'Lihat Menu & Data',
        'master_user.create'         => 'Tambah User',
        'master_user.edit'           => 'Edit User & Hak Akses',
        'master_user.toggle'         => 'Nonaktifkan / Aktifkan User',
        'master_karyawan'            => 'Master Karyawan',
        'master_karyawan.view'       => 'Lihat Menu & Data',
        'master_karyawan.create'     => 'Tambah Karyawan',
        'master_karyawan.edit'       => 'Edit Karyawan',
        'master_karyawan.toggle'     => 'Nonaktifkan / Aktifkan Karyawan',
        'master_barang'              => 'Master Barang',
        'master_barang.view'         => 'Lihat Menu & Data',
        'master_barang.create'       => 'Tambah Barang',
        'master_barang.edit'         => 'Edit Barang',
        'master_barang.toggle'       => 'Nonaktifkan / Aktifkan Barang',
        'master_pekerjaan'           => 'Master Pekerjaan',
        'master_pekerjaan.view'      => 'Lihat Menu & Data',
        'master_pekerjaan.create'    => 'Tambah Pekerjaan',
        'master_pekerjaan.edit'      => 'Edit Pekerjaan',
        'master_pekerjaan.toggle'    => 'Nonaktifkan / Aktifkan Pekerjaan',
        'master_lokasi_subcon'       => 'Master Lokasi Subcon',
        'master_lokasi_subcon.view'  => 'Lihat Menu & Data',
        'master_lokasi_subcon.create'=> 'Tambah Lokasi Subcon',
        'master_lokasi_subcon.edit'  => 'Edit Lokasi Subcon',
        'master_lokasi_subcon.toggle'=> 'Nonaktifkan / Aktifkan Lokasi Subcon',
        'formulir_pengerjaan'        => 'Formulir Pengerjaan',
        'formulir_pengerjaan.view'   => 'Buka Form & Input Pengerjaan',
        'laporan_subcon'             => 'Laporan Subcon',
        'laporan_subcon.view'        => 'Lihat & Export Laporan',
        'log_report'                 => 'Log Report',
        'log_report.view'            => 'Lihat Log Report',
    ];

    public static function getAllPermissions(): array
    {
        $flattened = [];
        foreach (self::PERMISSION_GROUPS as $group => $items) {
            foreach ($items as $key => $label) {
                $flattened[$key] = $label;
            }
        }
        return $flattened;
    }

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
     * Memeriksa apakah user memiliki hak akses ke suatu menu/aksi
     */
    public function canAccess(string $permission): bool
    {
        // Jika akun nonaktif, tolak akses
        if (!$this->is_active) {
            return false;
        }

        // Super Admin memiliki akses penuh ke seluruh menu dan aksi
        if ($this->role === self::ROLE_SUPER_ADMIN || ($this->is_admin && empty($this->permissions))) {
            return true;
        }

        if (!is_array($this->permissions)) {
            return false;
        }

        // 1. Direct match (misal: 'master_barang.create')
        if (in_array($permission, $this->permissions, true)) {
            return true;
        }

        // 2. Base module match untuk sidebar / menu access
        // Jika cek 'master_barang' (atau 'master_barang.view'), izinkan jika user memiliki aksi apapun di modul tersebut
        if (!str_contains($permission, '.')) {
            // Cek apakah punya permission base itu sendiri atau sub-permission apapun di modul tersebut
            if (in_array($permission, $this->permissions, true)) {
                return true;
            }
            foreach ($this->permissions as $p) {
                if (str_starts_with($p, $permission . '.')) {
                    return true;
                }
            }
        } else {
            // Jika cek 'master_barang.view', izinkan jika punya permission legacy 'master_barang'
            $base = explode('.', $permission)[0];
            if (in_array($base, $this->permissions, true)) {
                return true;
            }
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
