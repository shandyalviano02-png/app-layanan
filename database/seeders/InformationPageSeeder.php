<?php

namespace Database\Seeders;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use App\Models\DownloadableForm;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InformationPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@dinsos.blitarkab.go.id')->first()
            ?? User::first();

        $dtsenType = ServiceType::where('code', 'DTSEN')->first();
        $pbiType = ServiceType::where('code', 'PBI')->first();
        $rehsosType = ServiceType::where('code', 'REHSOS')->first();

        $pages = [
            [
                'title' => 'Layanan Surat Keterangan DTSEN Kabupaten Blitar',
                'slug' => 'surat-keterangan-dtsen',
                'category' => InformationCategory::Program,
                'service_type_id' => $dtsenType?->id,
                'description' => 'Panduan lengkap permohonan Surat Keterangan Data Tunggal Sosial Ekonomi Nasional (DTSEN) untuk keperluan SPMB/PPDB jalur afirmasi, beasiswa KIP Kuliah, PIP, dan bantuan sosial di Dinas Sosial Kabupaten Blitar.',
                'requirements' => "1. Foto/Scan Asli KTP Pemohon (Orang Tua/Wali)\n2. Foto/Scan Asli Kartu Keluarga (KK) yang masih berlaku\n3. Dokumen pendukung tambahan jika diperlukan (Surat Pengantar Sekolah/Kampus)",
                'procedure' => "1. Pemohon memilih tujuan penggunaan surat (SPMB, PIP, KIP Kuliah, bansos, dll.)\n2. Pemohon melengkapi data pemohon dan data orang yang diterangkan serta mengunggah berkas KTP & KK\n3. Sistem menerbitkan nomor tiket unik untuk pemantauan alur layanan\n4. Petugas memeriksa kelengkapan dokumen dan mengecek desil pemohon di SIKS-NG\n5. Draf surat diparaf berjenjang oleh Kepala Bidang dan ditandatangani Kepala Dinas\n6. Pemohon dapat mengunduh surat resmi ber-barcode verifikasi secara mandiri",
                'service_hours' => 'Senin - Kamis: 08.00 - 15.00 WIB | Jumat: 08.00 - 14.30 WIB',
                'location' => 'Kantor Dinas Sosial Kabupaten Blitar, Jl. Raya Kanigoro, Blitar',
                'contact' => 'Telp: (0342) 801234 | WhatsApp Pelayanan: 0812-3456-7890',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now(),
                'manager_id' => $admin->id,
                'forms' => [
                    [
                        'name' => 'Formulir Pernyataan Keabsahan Data DTSEN',
                        'file_path' => 'forms/formulir-keabsahan-dtsen-v1.pdf',
                        'version' => '1.0',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Apakah pengurusan Surat Keterangan DTSEN dipungut biaya?',
                        'answer' => 'Tidak dipungut biaya (GRATIS). Seluruh pelayanan di Dinas Sosial Kabupaten Blitar bebas dari pungutan liar.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Berapa desil maksimal agar SK DTSEN untuk SPMB dapat diterbitkan?',
                        'answer' => 'Berdasarkan regulasi yang berlaku, batas maksimal untuk keperluan SPMB/PPDB jalur afirmasi dan KIP Kuliah adalah desil 1 sampai dengan desil 5.',
                        'sort_order' => 2,
                    ],
                    [
                        'question' => 'Bagaimana jika nama saya belum terdaftar di DTKS / DTSEN?',
                        'answer' => 'Jika data belum terdaftar, pengajuan akan ditolak dengan penjelasan resmi. Pemohon disarankan berkoordinasi dengan operator SIKS-NG Desa/Kelurahan setempat untuk pengusulan DTKS/DTSEN baru melalui musyawarah desa/kelurahan (Musdes/Muskel).',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'title' => 'Layanan Reaktivasi Kepesertaan KIS / PBI-JK',
                'slug' => 'reaktivasi-kis-pbi-jk',
                'category' => InformationCategory::Program,
                'service_type_id' => $pbiType?->id,
                'description' => 'Fasilitasi pengaktifan kembali jaminan kesehatan JKN-KIS segmen Penerima Bantuan Iuran Jaminan Kesehatan (PBI-JK) yang dinonaktifkan oleh Kementerian Sosial.',
                'requirements' => "1. Foto/Scan Asli KTP Peserta\n2. Foto/Scan Asli Kartu Keluarga (KK)\n3. Foto/Scan Kartu BPJS Kesehatan / KIS yang nonaktif\n4. Surat keterangan rawat inap / resume medis dari Faskes/Rumah Sakit (Wajib untuk kondisi darurat/kronis)",
                'procedure' => "1. Pemohon mengajukan permohonan dengan menginput data peserta dan alasan reaktivasi\n2. Mengunggah dokumen persyaratan utama dan surat dokter jika kondisi darurat medis\n3. Petugas Dinsos memverifikasi desil dan kelayakan peserta di SIKS-NG\n4. Kepala Dinas menerbitkan Surat Rekomendasi Reaktivasi\n5. Petugas mengusulkan reaktivasi ke Kementerian Sosial melalui aplikasi SIKS-NG\n6. Petugas memantau status hingga kepesertaan aktif kembali di sistem BPJS Kesehatan",
                'service_hours' => 'Senin - Kamis: 08.00 - 15.00 WIB | Jumat: 08.00 - 14.30 WIB',
                'location' => 'Bidang Perlindungan dan Jaminan Sosial, Dinsos Kab. Blitar',
                'contact' => 'Helpdesk KIS: 0812-3456-7891',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now(),
                'manager_id' => $admin->id,
                'forms' => [
                    [
                        'name' => 'Formulir Permohonan Reaktivasi KIS PBI-JK',
                        'file_path' => 'forms/formulir-reaktivasi-pbi-v1.pdf',
                        'version' => '1.0',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Siapa yang berhak mengajukan reaktivasi PBI-JK?',
                        'answer' => 'Warga Kabupaten Blitar yang kepesertaan PBI-JK miliknya dinonaktifkan kurang dari 6 bulan dan membutuhkan penanganan medis segera (darurat medis, penyakit kronis/katastropik) serta memenuhi kriteria desil DTKS.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Bagaimana jika pasien sedang dirawat di IGD atau ICU?',
                        'answer' => 'Pengajuan dengan alasan kondisi darurat medis akan diprioritaskan oleh petugas untuk segera diterbitkan rekomendasi dalam waktu 1x24 jam kerja.',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'title' => 'Alur Penanganan dan Pelayanan Rehabilitasi Sosial (PPKS)',
                'slug' => 'pelayanan-rehabilitasi-sosial',
                'category' => InformationCategory::Rehabilitation,
                'service_type_id' => $rehsosType?->id,
                'description' => 'Mekanisme penanganan rehabilitasi sosial bagi lansia terlantar, penyandang disabilitas, ODGJ terlantar, dan anak terlantar melalui pelayanan terpadu dan rujukan panti.',
                'requirements' => "1. Identitas Klien (KTP/KK jika ada)\n2. Surat Pengantar dari Pemerintah Desa / Kelurahan\n3. Foto dokumentasi kondisi klien di lokasi",
                'procedure' => "1. Laporan kasus diterima oleh petugas atau rujukan pengaduan masyarakat\n2. Pekerja sosial melakukan assessment komprehensif terhadap kondisi fisik, mental, dan sosial klien\n3. Penyusunan rencana pelayanan (pelayanan langsung atau rujukan ke panti/RS)\n4. Pelaksanaan tindakan rujukan ke lembaga rujukan terkait\n5. Monitoring berkala terhadap perkembangan dan rehabilitasi klien",
                'service_hours' => 'Senin - Jumat: 08.00 - 15.00 WIB (Layanan aduan kedaruratan 24 Jam)',
                'location' => 'Bidang Rehabilitasi Sosial, Dinsos Kab. Blitar',
                'contact' => 'Hotline Rehsos: 0812-3456-7892',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now(),
                'manager_id' => $admin->id,
                'forms' => [],
                'faqs' => [
                    [
                        'question' => 'Apakah ada biaya perawatan saat klien dirujuk ke panti sosial milik pemerintah?',
                        'answer' => 'Seluruh biaya permakanan, akomodasi, dan pembinaan di panti sosial milik pemerintah (UPT Dinsos Provinsi Jatim) sepenuhnya gratis.',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'title' => 'Tata Cara Penyampaian Pengaduan Sosial Masyarakat',
                'slug' => 'pengaduan-dan-laporan-sosial',
                'category' => InformationCategory::Complaint,
                'service_type_id' => null,
                'description' => 'Kanal resmi pengaduan masalah sosial, penelantaran warga, bantuan sosial, dan pelayanan sosial di wilayah Kabupaten Blitar.',
                'requirements' => "1. Identitas Pelapor (Nama & Nomor HP aktif)\n2. Lokasi kejadian minimal Kecamatan dan Desa/Kelurahan\n3. Uraian kronologi kejadian / permasalahan\n4. Foto/Video bukti pendukung",
                'procedure' => "1. Pelapor mengisi formulir pengaduan publik dengan kategori yang sesuai\n2. Sistem menerbitkan nomor tiket pengaduan (ADU-YYYYMM-NNNNN)\n3. Petugas Dinsos memverifikasi laporan awal dan menghubungi pelapor bila butuh klarifikasi\n4. Laporan didisposisikan ke unit kerja / pekerja sosial terkait untuk penanganan lapangan\n5. Hasil penanganan didokumentasikan dan diupdate ke sistem sehingga dapat dilacak pelapor",
                'service_hours' => 'Layanan formulir online 24 jam',
                'location' => 'Dinas Sosial Kabupaten Blitar',
                'contact' => 'Unit Pengaduan: 0812-3456-7893',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now(),
                'manager_id' => $admin->id,
                'forms' => [],
                'faqs' => [
                    [
                        'question' => 'Apakah identitas pelapor dijamin kerahasiaannya?',
                        'answer' => 'Ya, identitas pelapor dilindungi dan hanya digunakan oleh petugas berwenang untuk koordinasi tindak lanjut.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Berapa lama pengaduan akan ditindaklanjuti?',
                        'answer' => 'Pengaduan kedaruratan sosial akan ditindaklanjuti dalam waktu maksimal 1x24 jam kerja, sedangkan pengaduan administratif 1–3 hari kerja.',
                        'sort_order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($pages as $item) {
            $forms = $item['forms'];
            $faqs = $item['faqs'];
            unset($item['forms'], $item['faqs']);

            $page = InformationPage::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );

            foreach ($forms as $form) {
                DownloadableForm::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'name' => $form['name'],
                    ],
                    $form
                );
            }

            foreach ($faqs as $faq) {
                Faq::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'question' => $faq['question'],
                    ],
                    $faq
                );
            }
        }
    }
}
