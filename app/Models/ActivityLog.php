<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'tb_activity_log';

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat log aktivitas secara otomatis
     *
     * @param string $module Contoh: 'Master Barang', 'Master User', 'Formulir Pengerjaan', 'Auth'
     * @param string $action Contoh: 'CREATE', 'UPDATE', 'DELETE', 'TOGGLE_STATUS', 'LOGIN', 'LOGOUT'
     * @param string $description Penjelasan ringkas aktivitas
     * @param User|null $actor User yang mengeksekusi jika berbeda dari Auth::user()
     * @return self|null
     */
    public static function record(string $module, string $action, string $description, ?User $actor = null): ?self
    {
        try {
            $user = $actor ?: Auth::user();
            $guestUsername = Request::input('username') ?: Request::input('name');

            return self::create([
                'user_id'     => $user?->id,
                'user_name'   => $user?->name ?: ($user?->username ?: ($guestUsername ? "Guest ({$guestUsername})" : 'Guest / System')),
                'role'        => $user?->role ?: 'guest',
                'action'      => strtoupper($action),
                'module'      => $module,
                'description' => $description,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Jangan gagalkan alur utama jika pencatatan log mengalami kendala
            \Illuminate\Support\Facades\Log::error('ActivityLog recording failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
