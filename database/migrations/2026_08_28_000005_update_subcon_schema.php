<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah user_id ke tb_lokasi_subcon untuk akun login Subcon
        Schema::table('tb_lokasi_subcon', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('tb_user')->onDelete('set null');
        });

        // 2. Tambah nama_karyawan dan lokasi_subcon_id ke tb_karyawan
        Schema::table('tb_karyawan', function (Blueprint $table) {
            $table->string('nama_karyawan')->nullable()->after('user_id');
            $table->foreignId('lokasi_subcon_id')->nullable()->after('nama_karyawan')->constrained('tb_lokasi_subcon')->onDelete('set null');
        });

        // Copy nama dari tb_user ke tb_karyawan untuk data yang sudah ada
        try {
            DB::statement("
                UPDATE tb_karyawan k
                JOIN tb_user u ON k.user_id = u.id
                SET k.nama_karyawan = u.name
                WHERE k.nama_karyawan IS NULL
            ");
        } catch (\Throwable $e) {
            // Ignore if empty
        }

        // 3. Buat tabel pivot tb_lokasi_subcon_barang untuk mapping barang per subcon
        Schema::create('tb_lokasi_subcon_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_subcon_id')->constrained('tb_lokasi_subcon')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('tb_barang')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['lokasi_subcon_id', 'barang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_lokasi_subcon_barang');

        Schema::table('tb_karyawan', function (Blueprint $table) {
            $table->dropForeign(['lokasi_subcon_id']);
            $table->dropColumn(['nama_karyawan', 'lokasi_subcon_id']);
        });

        Schema::table('tb_lokasi_subcon', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
