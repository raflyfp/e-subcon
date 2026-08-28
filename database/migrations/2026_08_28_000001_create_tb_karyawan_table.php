<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tb_user')->onDelete('cascade');
            $table->string('no_karyawan', 50)->unique();
            $table->string('telepon', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_karyawan');
    }
};
