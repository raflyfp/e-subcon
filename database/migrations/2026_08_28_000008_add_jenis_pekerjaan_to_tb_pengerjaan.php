<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_pengerjaan', function (Blueprint $table) {
            $table->string('jenis_pekerjaan', 100)->nullable()->after('lokasi_subcon_id');
        });
    }

    public function down(): void
    {
        Schema::table('tb_pengerjaan', function (Blueprint $table) {
            $table->dropColumn('jenis_pekerjaan');
        });
    }
};
