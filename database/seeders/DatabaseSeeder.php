<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data awal aplikasi e-Subcon
     */
    public function run(): void
    {
        // Admin user
        DB::table('tb_user')->insert([
            'name'       => 'Administrator',
            'username'   => 'admin',
            'password'   => Hash::make('admin123'),
            'is_admin'   => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sample karyawan user
        $userId = DB::table('tb_user')->insertGetId([
            'name'       => 'Karyawan Demo',
            'username'   => 'karyawan01',
            'password'   => Hash::make('12345'),
            'is_admin'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Link user to karyawan
        DB::table('tb_karyawan')->insert([
            'user_id'      => $userId,
            'no_karyawan'  => 'KRY-001',
            'telepon'      => '081234567890',
            'is_active'    => 1,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Sample barang
        $barangData = [
            ['kode_barang' => 'BRG-001', 'nama_barang' => 'Part A'],
            ['kode_barang' => 'BRG-002', 'nama_barang' => 'Part B'],
            ['kode_barang' => 'BRG-003', 'nama_barang' => 'Part C'],
        ];

        foreach ($barangData as $barang) {
            DB::table('tb_barang')->insert(array_merge($barang, [
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Sample lokasi subcon
        $lokasiData = [
            ['nama_lokasi' => 'Subcon Alpha', 'alamat' => 'Jl. Industri No. 1'],
            ['nama_lokasi' => 'Subcon Beta',  'alamat' => 'Jl. Raya No. 2'],
        ];

        foreach ($lokasiData as $lokasi) {
            DB::table('tb_lokasi_subcon')->insert(array_merge($lokasi, [
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
