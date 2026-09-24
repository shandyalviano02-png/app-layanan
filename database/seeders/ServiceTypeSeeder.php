<?php

namespace Database\Seeders;

use App\Enums\ServiceHandler;
use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'code' => 'DTSEN',
                'name' => 'Surat Keterangan DTSEN',
                'category' => 'Perlindungan dan Jaminan Sosial',
                'description' => 'Penerbitan surat keterangan yang menerangkan status seseorang/keluarga dalam Data Tunggal Sosial Ekonomi Nasional (DTSEN) dan peringkat desil untuk syarat SPMB afirmasi, PIP, KIP Kuliah, bansos, dan kesehatan.',
                'handler' => ServiceHandler::Dtsen,
                'needs_assessment' => false,
                'sla_days' => 1,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon / Orang Tua',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'code' => 'PBI',
                'name' => 'Reaktivasi KIS / PBI-JK',
                'category' => 'Jaminan Kesehatan',
                'description' => 'Fasilitasi pengaktifan kembali kepesertaan JKN-KIS Penerima Bantuan Iuran (PBI-JK) yang dinonaktifkan dengan verifikasi kelayakan desil dan rekomendasi ke Kementerian Sosial.',
                'handler' => ServiceHandler::Pbi,
                'needs_assessment' => false,
                'sla_days' => 3,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon / Peserta',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Kartu BPJS / KIS Nonaktif',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Surat Keterangan Rawat / Faskes (Wajib untuk Darurat / Kronis)',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'REHSOS',
                'name' => 'Permohonan Pelayanan Rehabilitasi Sosial',
                'category' => 'Rehabilitasi Sosial',
                'description' => 'Penanganan dan pelayanan sosial bagi Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) seperti lansia terlantar, disabilitas, ODGJ, dan anak terlantar melalui pelayanan langsung atau rujukan.',
                'handler' => ServiceHandler::Generic,
                'needs_assessment' => true,
                'sla_days' => 7,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon / Wali (Jika Ada)',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (Jika Ada)',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Pengantar / Keterangan Desa / Kelurahan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Foto Kondisi Calon Klien PPKS',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'BANSOS',
                'name' => 'Rekomendasi Bantuan Sosial Terencana',
                'category' => 'Pemberdayaan Sosial & Fakir Miskin',
                'description' => 'Permohonan rekomendasi bantuan sosial terencana bagi keluarga miskin/rentan miskin di Kabupaten Blitar.',
                'handler' => ServiceHandler::Generic,
                'needs_assessment' => true,
                'sla_days' => 5,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Keterangan Tidak Mampu (SKTM) dari Desa/Kelurahan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Foto Rumah Tampak Depan & Ruang Utama',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];

        foreach ($services as $data) {
            $requirements = $data['requirements'];
            unset($data['requirements']);

            $serviceType = ServiceType::updateOrCreate(
                ['code' => $data['code']],
                $data
            );

            foreach ($requirements as $req) {
                ServiceRequirement::updateOrCreate(
                    [
                        'service_type_id' => $serviceType->id,
                        'name' => $req['name'],
                    ],
                    $req
                );
            }
        }
    }
}
