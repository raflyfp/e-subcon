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

        // Sample subcon user
        $subconUserId = DB::table('tb_user')->insertGetId([
            'name'       => 'Subcon Alpha',
            'username'   => 'subcon01',
            'password'   => Hash::make('12345'),
            'is_admin'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sample lokasi subcon
        $lokasiAlphaId = DB::table('tb_lokasi_subcon')->insertGetId([
            'user_id'     => $subconUserId,
            'nama_lokasi' => 'Subcon Alpha',
            'alamat'      => 'Jl. Industri No. 1',
            'is_active'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $lokasiBetaId = DB::table('tb_lokasi_subcon')->insertGetId([
            'user_id'     => null,
            'nama_lokasi' => 'Subcon Beta',
            'alamat'      => 'Jl. Raya No. 2',
            'is_active'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Sample karyawan
        DB::table('tb_karyawan')->insert([
            'nama_karyawan'    => 'Budi Santoso',
            'no_karyawan'      => 'KRY-001',
            'lokasi_subcon_id' => $lokasiAlphaId,
            'is_active'        => 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Sample barang
        $barangData = [
            ['kode_barang' => 'BRG-001', 'nama_barang' => 'Part A', 'satuan' => 'PCS', 'lokasi_subcon_id' => $lokasiAlphaId],
            ['kode_barang' => 'BRG-002', 'nama_barang' => 'Part B', 'satuan' => 'UNIT', 'lokasi_subcon_id' => $lokasiAlphaId],
            ['kode_barang' => 'BRG-003', 'nama_barang' => 'Part C', 'satuan' => 'LEMBAR', 'lokasi_subcon_id' => $lokasiBetaId],
        ];

        foreach ($barangData as $barang) {
            DB::table('tb_barang')->insert(array_merge($barang, [
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
