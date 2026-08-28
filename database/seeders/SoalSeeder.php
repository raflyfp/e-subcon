<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class SoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $kriteria = [
            "Menjaga hubungan kerja yang baik dengan tim dan bagian lain.",
            "Memberikan arahan dan tujuan yang jelas kepada tim sehingga pekerjaan selesai sesuai dengan target waktu.",
            "Mendorong partisipasi dan kontribusi anggota tim.",
            "Menjadi role model dalam sikap dan etika kerja.",
            "Pekerjaan jarang mengalami keterlambatan akibat kurang perencanaan.",
            "Mengkoordinasikan pekerjaan sehingga tidak terjadi miskomunikasi antar bagian.",
            "Memberi informasi jika ada perubahan rencana atau jadwal.",
            "Mengelola waktu kerja tim dengan baik.",
            "Memberikan arahan yang membantu penyelesaian pekerjaan secara benar.",
            "Memberikan feedback secara membangun.",
            "Terbuka menerima masukan terkait pekerjaan.",
            "Bertanggung jawab atas hasil kerja tim.",
            "Mampu mengidentifikasi akar masalah.",
            "Menggunakan data atau fakta dalam menganalisis situasi.",
            "Mengevaluasi alternatif solusi sebelum bertindak.",
            "Menjelaskan analisis secara sistematis.",
            "Mengambil keputusan tepat waktu dan dapat diaplikasikan.",
            "Mempertimbangkan risiko dan dampak keputusan.",
            "Melibatkan pihak terkait saat diperlukan.",
            "Bertanggung jawab atas keputusan yang dibuat."
        ];

        foreach ($kriteria as $item) {
            DB::table('tb_kriteria')->insert([
                'soal' => $item,
                'jenis' => "leadership",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $kriteriaCulture = [
            "Melakukan pekerjaan dengan cara dan motivasi yang tepat",
            "Memahami dan melaksanakan job desk serta tanggung jawab sebaik mungkin",
            "Meningkatkan kemampuan teknis/fungsional, inti dan manajerial",
            "Antusiasme secara verbal dan non verbal terhadap program perusahaan",
            "Fokus menyelesaikan pekerjaan pada jam efektif kerja",
            "Memenuhi segala kewajiban tanpa harus diawasi",
            "Melakukan upaya yang diperlukan dengan cara yang efektif dan efisien",
            "Memiliki ambisi untuk selalu maju tetapi tetap mempunyai empati",
            "Bersikap sama berat, jujur, obyektif terhadap perusahaan dan semua rekan kerja di segala posisi",
            "Memahami apa yang menjadi kelebihan serta kekurangan diri",
            "Melakukan perbaikan secara berkelanjutan di area kerja maupun perusahaan",
            "Menyampaikan laporan dengan data, bukan bercerita",
            "Melakukan tindakan untuk mengurangi dampak dari segala situasi dengan cara yang benar",
            "Belajar dari atasan (untuk bawahan) dan mencetak leader baru (untuk atasan)",
            "Hilangkan hambatan mental, pemikiran negatif, ujaran kebencian dan SARA",
            "Kesadaran untuk segera bertindak karena memahami pentingnya waktu dan konsekuensi penundaan",
            "Menyelesaikan masalah tanpa menambah masalah lain, menemukan solusi yang efisien dan efektif",
            "Membuat keputusan yang relevan, sesuai dan dapat diimplementasikan",
            "Melakukan perbaikan terhadap kesalahan diri sendiri maupun orang lain (agar tidak terulang)",
            "Menunjukkan komitmen dengan membuktikan perkataannya melalui tindakan nyata"
        ];

        foreach ($kriteriaCulture as $item) {
            DB::table('tb_kriteria')->insert([
                'soal' => $item,
                'jenis' => "culture",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
