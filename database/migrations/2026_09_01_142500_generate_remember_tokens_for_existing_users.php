<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $users = DB::table('tb_user')->whereNull('remember_token')->orWhere('remember_token', '')->get();
        foreach ($users as $user) {
            DB::table('tb_user')->where('id', $user->id)->update([
                'remember_token' => Str::random(60),
            ]);
        }
    }

    public function down(): void
    {
        // No need to revert random tokens
    }
};
