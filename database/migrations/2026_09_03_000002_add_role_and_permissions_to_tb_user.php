<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->string('role', 50)->default('admin_biasa')->after('is_admin');
            $table->json('permissions')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('permissions');
        });

        // Set default role dan permission untuk data user yang sudah ada
        $allPermissions = json_encode([
            'dashboard',
            'master_user',
            'master_karyawan',
            'master_barang',
            'master_pekerjaan',
            'master_lokasi_subcon',
            'formulir_pengerjaan',
            'laporan_subcon'
        ]);

        $subconPermissions = json_encode([
            'dashboard',
            'formulir_pengerjaan',
            'laporan_subcon'
        ]);

        // User admin yang ada menjadi super_admin dengan akses penuh
        DB::table('tb_user')
            ->where('is_admin', 1)
            ->update([
                'role'        => 'super_admin',
                'permissions' => $allPermissions,
                'is_active'   => 1,
            ]);

        // User subcon yang ada menjadi role subcon
        DB::table('tb_user')
            ->where('is_admin', 0)
            ->update([
                'role'        => 'subcon',
                'permissions' => $subconPermissions,
                'is_active'   => 1,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropColumn(['role', 'permissions', 'is_active']);
        });
    }
};
