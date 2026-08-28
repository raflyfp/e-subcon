<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\AnggotaDepartemen;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            $path = storage_path('app/tb_karyawan.csv');

            $rows = array_map('str_getcsv', file($path));
            $header = array_shift($rows);

            foreach ($rows as $row) {

                $data = array_combine($header, $row);

                // =====================
                // 1. TB USER
                // =====================
                $user = User::firstOrCreate(
                    ['username' => $data['nik_karyawan']],
                    [
                        'name' => $data['nama_karyawan'],
                        'password' => Hash::make('12345'),
                        'is_admin' => 0
                    ]
                );

                // =====================
                // 2. TB KARYAWAN
                // =====================
                $karyawan = Karyawan::firstOrCreate(
                    ['user_id' => $user->id],
                    ['atasan_id' => null]
                );

                // simpan mapping sementara
                $map[$data['nik_karyawan']] = $karyawan->id;
            }

            // =====================
            // 3. UPDATE ATASAN
            // =====================

            foreach ($rows as $row) {

                $data = array_combine($header, $row);

                if (empty($data['atasan_karyawan'])) continue;

                $user = User::where('username', $data['nik_karyawan'])->first();
                $karyawan = Karyawan::where('user_id', $user->id)->first();

                $atasanUser = User::where('name', $data['atasan_karyawan'])->first();

                if ($atasanUser) {

                    $atasan = Karyawan::where('user_id', $atasanUser->id)->first();

                    if ($atasan) {
                        $karyawan->update([
                            'atasan_id' => $atasan->id
                        ]);
                    }
                }
            }

            // =====================
            // 4. TB ANGGOTA DEPARTEMEN
            // =====================

            foreach ($rows as $row) {

                $data = array_combine($header, $row);

                $user = User::where('username', $data['nik_karyawan'])->first();
                $karyawan = Karyawan::where('user_id', $user->id)->first();

                $departemenId = DB::table('tb_departemen')
                    ->where('departemen', $data['nama_dept'])
                    ->value('id');

                $jabatanId = DB::table('tb_jabatan')
                    ->where('jabatan', $data['jabatan_karyawan'])
                    ->value('id');

                if ($departemenId && $jabatanId) {

                    AnggotaDepartemen::firstOrCreate([
                        'karyawan_id' => $karyawan->id,
                        'departemen_id' => $departemenId,
                        'jabatan_id' => $jabatanId,
                    ]);
                }
            }

            DB::commit();

            echo "Seeder CSV berhasil dijalankan\n";

        } catch (\Throwable $e) {

            DB::rollBack();

            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
    }
}
