<?php

namespace Database\Seeders;

use App\Models\ReferralInstitution;
use Illuminate\Database\Seeder;

class ReferralInstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'UPT Pelayanan Sosial Tresna Werdha (PSTW) Blitar',
                'type' => 'panti',
                'address' => 'Jl. Merdeka No. 12, Blitar',
                'contact' => '0342-801234',
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Ngudi Waluyo Wlingi',
                'type' => 'RS',
                'address' => 'Jl. Dokter Suwandhi No. 5, Wlingi, Kabupaten Blitar',
                'contact' => '0342-691006',
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Srengat Kabupaten Blitar',
                'type' => 'RS',
                'address' => 'Jl. Raya Dandong, Srengat, Kabupaten Blitar',
                'contact' => '0342-567890',
                'is_active' => true,
            ],
            [
                'name' => 'RSJ Dr. Radjiman Wediodiningrat Lawang (Rujukan Jiwa/ODGJ)',
                'type' => 'RS',
                'address' => 'Jl. Ahmad Yani, Lawang, Malang',
                'contact' => '0341-426015',
                'is_active' => true,
            ],
            [
                'name' => 'Balai Rehabilitasi Sosial Penyandang Disabilitas Netra / Fisik',
                'type' => 'balai',
                'address' => 'Jl. Veteran No. 45, Blitar',
                'contact' => '0342-802345',
                'is_active' => true,
            ],
            [
                'name' => 'LKS Lembaga Kesejahteraan Sosial Anak Harapan Bangsa',
                'type' => 'LKS',
                'address' => 'Kecamatan Kanigoro, Kabupaten Blitar',
                'contact' => '081234567812',
                'is_active' => true,
            ],
        ];

        foreach ($institutions as $item) {
            ReferralInstitution::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
