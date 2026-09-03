<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->foreignId('pekerjaan_id')->nullable()->after('lokasi_subcon_id')->constrained('tb_pekerjaan')->nullOnDelete();
            $table->string('jenis_pekerjaan', 100)->nullable()->after('pekerjaan_id');
        });
    }

    public function down(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->dropForeign(['pekerjaan_id']);
            $table->dropColumn(['pekerjaan_id', 'jenis_pekerjaan']);
        });
    }
};
