<?php

namespace Database\Seeders;

use App\Models\ClientCategory;
use Illuminate\Database\Seeder;

class ClientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Lanjut Usia Terlantar (Lansia)',
            'Penyandang Disabilitas (Fisik / Sensorik / Intelektual / Mental)',
            'Orang Dengan Gangguan Jiwa (ODGJ) Terlantar',
            'Anak Terlantar / Anak Membutuhkan Perlindungan Khusus (AMPK)',
            'Korban Tindak Kekerasan (KTK) & Perdagangan Orang',
            'Gelandangan dan Pengemis (Gepeng)',
            'Keluarga Rentan Masalah Sosial Psikologis',
        ];

        foreach ($categories as $name) {
            ClientCategory::updateOrCreate(['name' => $name]);
        }
    }
}
