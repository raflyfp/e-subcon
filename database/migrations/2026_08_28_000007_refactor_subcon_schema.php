<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus tabel pivot tb_lokasi_subcon_barang jika ada
        Schema::dropIfExists('tb_lokasi_subcon_barang');

        // 2. Modifikasi tb_barang: tambah lokasi_subcon_id dan satuan
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->foreignId('lokasi_subcon_id')->nullable()->after('id')->constrained('tb_lokasi_subcon')->onDelete('set null');
            $table->string('satuan', 50)->default('PCS')->after('nama_barang');
        });

        // 3. Modifikasi tb_karyawan: hapus user_id dan telepon
        Schema::table('tb_karyawan', function (Blueprint $table) {
            // Drop foreign key user_id jika ada
            if (Schema::hasColumn('tb_karyawan', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Throwable $e) {
                    // Ignore if foreign key doesn't exist
                }
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('tb_karyawan', 'telepon')) {
                $table->dropColumn('telepon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_karyawan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('tb_user')->onDelete('cascade');
            $table->string('telepon', 20)->nullable()->after('no_karyawan');
        });

        Schema::table('tb_barang', function (Blueprint $table) {
            $table->dropForeign(['lokasi_subcon_id']);
            $table->dropColumn(['lokasi_subcon_id', 'satuan']);
        });

        Schema::create('tb_lokasi_subcon_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_subcon_id')->constrained('tb_lokasi_subcon')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('tb_barang')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['lokasi_subcon_id', 'barang_id']);
        });
    }
};
