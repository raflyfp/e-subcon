<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('tb_jabatan')->insert([
            ['jabatan' => 'ADMIN'],
            ['jabatan' => 'LEADER'],
            ['jabatan' => 'Leader POD'],
            ['jabatan' => 'OPERATOR'],
            ['jabatan' => 'Manager'],
            ['jabatan' => 'SECURITY'],
            ['jabatan' => 'SPV'],
            ['jabatan' => 'STAF'],
            ['jabatan' => 'Staf Document Controller'],
            ['jabatan' => 'Staff POD'],
            ['jabatan' => 'Direktur'],
            ['jabatan' => 'Storeman'],
            ['jabatan' => 'Warehouse Finishgood'],
        ]);
    }
}
