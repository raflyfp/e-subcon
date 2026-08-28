<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pengerjaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('tb_karyawan')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('tb_barang')->onDelete('cascade');
            $table->foreignId('lokasi_subcon_id')->constrained('tb_lokasi_subcon')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pengerjaan');
    }
};
