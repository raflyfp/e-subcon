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

        // 4. Hubungkan Karyawan ke Subcon
        Karyawan::where('id', 1)->update([
            'lokasi_subcon_id' => $subcon1->id,
            'nama_karyawan'    => 'ALDO',
        ]);

        Karyawan::where('id', 2)->update([
            'lokasi_subcon_id' => $subcon2->id,
            'nama_karyawan'    => 'GUSTA',
        ]);

        // 5. Hubungkan Barang ke Subcon
        $allBarangIds = Barang::pluck('id')->toArray();
        if (!empty($allBarangIds)) {
            $subcon1->barang()->sync($allBarangIds);
            $subcon2->barang()->sync(array_slice($allBarangIds, 0, 2));
        }
    }
}
