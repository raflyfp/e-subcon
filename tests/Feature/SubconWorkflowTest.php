<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Karyawan;
use App\Models\LokasiSubcon;
use App\Models\Pengerjaan;
use App\Models\User;
use Database\Seeders\SubconSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubconWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SubconSeeder::class);
    }

    /**
     * Test: Tamu tanpa login diarahkan ke login
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));

        $responseForm = $this->get('/pengerjaan');
        $responseForm->assertRedirect(route('login'));
    }

    /**
     * Test: Akun Subcon login, membuka form pengerjaan, dan simpan data
     */
    public function test_subcon_can_view_form_and_store_pengerjaan(): void
    {
        $user = User::where('username', 'subcon1')->first();
        $this->assertNotNull($user);

        $subcon = $user->lokasiSubcon;
        $this->assertNotNull($subcon);

        $karyawan = Karyawan::where('lokasi_subcon_id', $subcon->id)->first();
        $barang = $subcon->barang()->first() ?? Barang::first();

        // 1. Kunjungi form pengerjaan
        $response = $this->actingAs($user)->get('/pengerjaan');
        $response->assertStatus(200);
        $response->assertSee($subcon->nama_lokasi);

        // 2. Simpan data pengerjaan
        $postData = [
            'karyawan_id'     => $karyawan->id,
            'barang_id'       => $barang->id,
            'jenis_pekerjaan' => 'Assembling',
            'tanggal'         => now()->toDateString(),
            'jumlah'          => 25,
            'keterangan'      => 'Test pengerjaan Subcon 1',
        ];

        $postResponse = $this->actingAs($user)->post('/pengerjaan/tambah', $postData);
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('tb_pengerjaan', [
            'karyawan_id'      => $karyawan->id,
            'barang_id'        => $barang->id,
            'lokasi_subcon_id' => $subcon->id,
            'jenis_pekerjaan'  => 'Assembling',
            'jumlah'           => 25,
        ]);
    }

    /**
     * Test: Laporan Subcon terisolasi hanya untuk subcon yang bersangkutan
     */
    public function test_subcon_report_is_isolated(): void
    {
        $user1 = User::where('username', 'subcon1')->first();
        $user2 = User::where('username', 'subcon2')->first();

        $subcon1 = $user1->lokasiSubcon;
        $subcon2 = $user2->lokasiSubcon;

        $karyawan1 = Karyawan::where('lokasi_subcon_id', $subcon1->id)->first();
        $karyawan2 = Karyawan::where('lokasi_subcon_id', $subcon2->id)->first();
        $barang1 = Barang::where('lokasi_subcon_id', $subcon1->id)->first();
        $barang2 = Barang::where('lokasi_subcon_id', $subcon2->id)->first();

        // Buat data pengerjaan untuk subcon 1 dan subcon 2
        Pengerjaan::create([
            'karyawan_id'      => $karyawan1->id,
            'barang_id'        => $barang1->id,
            'lokasi_subcon_id' => $subcon1->id,
            'jenis_pekerjaan'  => 'Cutting',
            'tanggal'          => now()->toDateString(),
            'jumlah'           => 10,
        ]);

        Pengerjaan::create([
            'karyawan_id'      => $karyawan2->id,
            'barang_id'        => $barang2->id,
            'lokasi_subcon_id' => $subcon2->id,
            'jenis_pekerjaan'  => 'Finishing',
            'tanggal'          => now()->toDateString(),
            'jumlah'           => 20,
        ]);

        // Subcon 1 melihat laporan
        $response = $this->actingAs($user1)->get('/laporan-subcon?filter=1');
        $response->assertStatus(200);
        
        $pengerjaanData = $response->viewData('pengerjaan');
        $this->assertNotEmpty($pengerjaanData);
        foreach ($pengerjaanData as $row) {
            $this->assertEquals($subcon1->nama_lokasi, $row->nama_lokasi);
            $this->assertNotEquals($subcon2->nama_lokasi, $row->nama_lokasi);
        }
    }

    /**
     * Test: Subcon can submit pengerjaan with checkbox jenis_pekerjaan
     */
    public function test_subcon_can_store_with_checkbox_jenis_pekerjaan(): void
    {
        $user = User::where('username', 'subcon1')->first();
        $subcon = $user->lokasiSubcon;
        $karyawan = Karyawan::where('lokasi_subcon_id', $subcon->id)->first();
        $barang = Barang::where('lokasi_subcon_id', $subcon->id)->first();

        $postData = [
            'karyawan_id'     => $karyawan->id,
            'barang_id'       => $barang->id,
            'tanggal'         => now()->toDateString(),
            'jenis_pekerjaan' => ['Folding'],
            'jumlah'          => 50,
            'keterangan'      => 'Pengerjaan Folding 50 PCS',
        ];

        $postResponse = $this->actingAs($user)->post('/pengerjaan/tambah', $postData);
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('tb_pengerjaan', [
            'karyawan_id'      => $karyawan->id,
            'barang_id'        => $barang->id,
            'lokasi_subcon_id' => $subcon->id,
            'jenis_pekerjaan'  => 'Folding',
            'jumlah'           => 50,
        ]);
    }

    /**
     * Test: Dashboard menampilkan monitoring pengisian karyawan dan navigasi tanggal
     */
    public function test_dashboard_monitoring_and_date_navigation(): void
    {
        $user = User::where('username', 'subcon1')->first();
        $subcon = $user->lokasiSubcon;
        $karyawan = Karyawan::where('lokasi_subcon_id', $subcon->id)->first();
        $barang = Barang::where('lokasi_subcon_id', $subcon->id)->first();

        // 1. Kunjungi dashboard default (hari ini)
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Status Pengisian Form Karyawan');
        $response->assertSee('Isi Formulir Pengerjaan');
        $response->assertSee($karyawan->nama_karyawan);
        $response->assertSee('Belum Isi');

        // 2. Tambah pengerjaan untuk hari ini
        Pengerjaan::create([
            'karyawan_id'      => $karyawan->id,
            'barang_id'        => $barang->id,
            'lokasi_subcon_id' => $subcon->id,
            'jenis_pekerjaan'  => 'Folding',
            'tanggal'          => now()->toDateString(),
            'jumlah'           => 75,
        ]);

        // 3. Kunjungi dashboard lagi - harus terupdate menjadi 'Sudah Isi'
        $responseToday = $this->actingAs($user)->get('/dashboard');
        $responseToday->assertStatus(200);
        $responseToday->assertSee('Sudah Isi');
        $responseToday->assertSee('75');

        // 4. Navigasi ke tanggal kemarin (seharusnya 'Belum Isi')
        $yesterday = now()->subDay()->toDateString();
        $responseYesterday = $this->actingAs($user)->get('/dashboard?tanggal=' . $yesterday);
        $responseYesterday->assertStatus(200);
        $responseYesterday->assertSee('Belum Isi');
    }
}
