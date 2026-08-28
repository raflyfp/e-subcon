<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table("tb_departemen")->insert([
            ['departemen' => 'Accounting'],
            ['departemen' => 'Antiseptic'],
            ['departemen' => 'Cotton Wool'],
            ['departemen' => 'Development'],
            ['departemen' => 'E-commerce'],
            ['departemen' => 'Finishing'],
            ['departemen' => 'Finishing Cotton Wool'],
            ['departemen' => 'GA'],
            ['departemen' => 'HR-Legal'],
            ['departemen' => 'HRD'],
            ['departemen' => 'HRD & BP'],
            ['departemen' => 'HRGA'],
            ['departemen' => 'IT'],
            ['departemen' => 'Logistik'],
            ['departemen' => 'MEKANIK'],
            ['departemen' => 'Orthopaedic & Tenun'],
            ['departemen' => 'PBF'],
            ['departemen' => 'PLESTER'],
            ['departemen' => 'PLESTER & Orthopaedic'],
            ['departemen' => 'POD'],
            ['departemen' => 'PPIC'],
            ['departemen' => 'PRODUKSI - MEKANIK'],
            ['departemen' => 'Purchasing'],
            ['departemen' => 'QA'],
            ['departemen' => 'Research'],
            ['departemen' => 'SCM'],
            ['departemen' => 'Security'],
            ['departemen' => 'Surgical'],
            ['departemen' => 'Warehouse'],
            ['departemen' => 'Manajemen'],
        ]);
    }
}
