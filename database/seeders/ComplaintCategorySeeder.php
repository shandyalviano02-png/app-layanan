<?php

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use Illuminate\Database\Seeder;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Warga Terlantar / Kedaruratan Sosial',
            'Permasalahan Bantuan Sosial (PKH, BPNT, BST, dll.)',
            'Kepesertaan KIS / PBI Nonaktif Sepihak',
            'Penelantaran & Kekerasan terhadap Anak / Lansia',
            'ODGJ Terlantar & Meresahkan Lingkungan',
            'Dugaan Ketidaktepatan Sasaran Bansos / Desil DTSEN',
            'Pelayanan Sosial Lainnya',
        ];

        foreach ($categories as $name) {
            ComplaintCategory::updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
