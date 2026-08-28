<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Karyawan;
use App\Models\LokasiSubcon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SubconSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('admin123'),
                'is_admin' => 1,
            ]
        );

        // 2. Subcon 1 Account
        $userSubcon1 = User::firstOrCreate(
            ['username' => 'subcon1'],
            [
                'name'     => 'Subcon 1',
                'password' => Hash::make('12345'),
                'is_admin' => 0,
            ]
        );

        $subcon1 = LokasiSubcon::firstOrCreate(
            ['nama_lokasi' => 'Subcon 1'],
            [
                'alamat'    => 'Jl. Industri No. 1',
                'is_active' => true,
            ]
        );
        $subcon1->update(['user_id' => $userSubcon1->id]);

        // 3. Subcon 2 Account
        $userSubcon2 = User::firstOrCreate(
            ['username' => 'subcon2'],
            [
                'name'     => 'Subcon 2',
                'password' => Hash::make('12345'),
                'is_admin' => 0,
            ]
        );

        $subcon2 = LokasiSubcon::firstOrCreate(
            ['nama_lokasi' => 'Subcon 2'],
            [
                'alamat'    => 'Jl. Raya No. 2',
                'is_active' => true,
            ]
        );
        $subcon2->update(['user_id' => $userSubcon2->id]);

        // 4. Karyawan per Subcon
        Karyawan::firstOrCreate(
            ['no_karyawan' => '001'],
            [
                'nama_karyawan'    => 'ALDO',
                'lokasi_subcon_id' => $subcon1->id,
                'is_active'        => true,
            ]
        );

        Karyawan::firstOrCreate(
            ['no_karyawan' => '002'],
            [
                'nama_karyawan'    => 'GUSTA',
                'lokasi_subcon_id' => $subcon2->id,
                'is_active'        => true,
            ]
        );

        // 5. Barang per Subcon
        Barang::firstOrCreate(
            ['kode_barang' => 'BRG-01'],
            [
                'nama_barang'      => 'Barang A',
                'satuan'           => 'PCS',
                'lokasi_subcon_id' => $subcon1->id,
                'is_active'        => true,
            ]
        );

        Barang::firstOrCreate(
            ['kode_barang' => 'BRG-02'],
            [
                'nama_barang'      => 'Barang B',
                'satuan'           => 'UNIT',
                'lokasi_subcon_id' => $subcon1->id,
                'is_active'        => true,
            ]
        );

        Barang::firstOrCreate(
            ['kode_barang' => 'BRG-03'],
            [
                'nama_barang'      => 'Barang C',
                'satuan'           => 'LEMBAR',
                'lokasi_subcon_id' => $subcon2->id,
                'is_active'        => true,
            ]
        );
    }
}
