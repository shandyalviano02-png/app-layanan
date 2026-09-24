<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [
                'code' => '35.05.01',
                'name' => 'Kanigoro',
                'villages' => [
                    ['code' => '35.05.01.1001', 'name' => 'Kanigoro'],
                    ['code' => '35.05.01.1002', 'name' => 'Satreyan'],
                    ['code' => '35.05.01.2003', 'name' => 'Tlogo'],
                    ['code' => '35.05.01.2004', 'name' => 'Bangle'],
                    ['code' => '35.05.01.2005', 'name' => 'Gaprang'],
                    ['code' => '35.05.01.2006', 'name' => 'Gogodeso'],
                    ['code' => '35.05.01.2007', 'name' => 'Jatinom'],
                    ['code' => '35.05.01.2008', 'name' => 'Karangsono'],
                    ['code' => '35.05.01.2009', 'name' => 'Kuningan'],
                    ['code' => '35.05.01.2010', 'name' => 'Minggirsari'],
                    ['code' => '35.05.01.2011', 'name' => 'Papungan'],
                    ['code' => '35.05.01.2012', 'name' => 'Sawentar'],
                ],
            ],
            [
                'code' => '35.05.02',
                'name' => 'Garum',
                'villages' => [
                    ['code' => '35.05.02.1001', 'name' => 'Garum'],
                    ['code' => '35.05.02.1002', 'name' => 'Tawangsari'],
                    ['code' => '35.05.02.1003', 'name' => 'Bence'],
                    ['code' => '35.05.02.2004', 'name' => 'Pojok'],
                    ['code' => '35.05.02.2005', 'name' => 'Sidodadi'],
                    ['code' => '35.05.02.2006', 'name' => 'Slorok'],
                    ['code' => '35.05.02.2007', 'name' => 'Tingal'],
                ],
            ],
            [
                'code' => '35.05.03',
                'name' => 'Talun',
                'villages' => [
                    ['code' => '35.05.03.1001', 'name' => 'Talun'],
                    ['code' => '35.05.03.1002', 'name' => 'Kamulan'],
                    ['code' => '35.05.03.2003', 'name' => 'Bendosewu'],
                    ['code' => '35.05.03.2004', 'name' => 'Duren'],
                    ['code' => '35.05.03.2005', 'name' => 'Jabung'],
                    ['code' => '35.05.03.2006', 'name' => 'Jajar'],
                    ['code' => '35.05.03.2007', 'name' => 'Kendalrejo'],
                    ['code' => '35.05.03.2008', 'name' => 'Pasirharjo'],
                    ['code' => '35.05.03.2009', 'name' => 'Sragi'],
                    ['code' => '35.05.03.2010', 'name' => 'Tumpang'],
                ],
            ],
            [
                'code' => '35.05.04',
                'name' => 'Wlingi',
                'villages' => [
                    ['code' => '35.05.04.1001', 'name' => 'Wlingi'],
                    ['code' => '35.05.04.1002', 'name' => 'Beru'],
                    ['code' => '35.05.04.1003', 'name' => 'Babadan'],
                    ['code' => '35.05.04.1004', 'name' => 'Klemunan'],
                    ['code' => '35.05.04.1005', 'name' => 'Tangkil'],
                    ['code' => '35.05.04.2006', 'name' => 'Tembalang'],
                    ['code' => '35.05.04.2007', 'name' => 'Tegalasri'],
                ],
            ],
            [
                'code' => '35.05.05',
                'name' => 'Srengat',
                'villages' => [
                    ['code' => '35.05.05.1001', 'name' => 'Srengat'],
                    ['code' => '35.05.05.1002', 'name' => 'Dandong'],
                    ['code' => '35.05.05.1003', 'name' => 'Kauman'],
                    ['code' => '35.05.05.1004', 'name' => 'Togogan'],
                    ['code' => '35.05.05.2005', 'name' => 'Bagelenan'],
                    ['code' => '35.05.05.2006', 'name' => 'Dermojayan'],
                    ['code' => '35.05.05.2007', 'name' => 'Kandangan'],
                    ['code' => '35.05.05.2008', 'name' => 'Purwokerto'],
                    ['code' => '35.05.05.2009', 'name' => 'Wonorejo'],
                ],
            ],
            [
                'code' => '35.05.06',
                'name' => 'Kademangan',
                'villages' => [
                    ['code' => '35.05.06.1001', 'name' => 'Kademangan'],
                    ['code' => '35.05.06.2002', 'name' => 'Rejowinangun'],
                    ['code' => '35.05.06.2003', 'name' => 'Bendosari'],
                    ['code' => '35.05.06.2004', 'name' => 'Darungan'],
                    ['code' => '35.05.06.2005', 'name' => 'Dawuhan'],
                    ['code' => '35.05.06.2006', 'name' => 'Jimbe'],
                    ['code' => '35.05.06.2007', 'name' => 'Suruhwadang'],
                ],
            ],
            [
                'code' => '35.05.07',
                'name' => 'Sutojayan',
                'villages' => [
                    ['code' => '35.05.07.1001', 'name' => 'Kembangarum'],
                    ['code' => '35.05.07.1002', 'name' => 'Sukorejo'],
                    ['code' => '35.05.07.1003', 'name' => 'Kalipang'],
                    ['code' => '35.05.07.2004', 'name' => 'Pandanarum'],
                    ['code' => '35.05.07.2005', 'name' => 'Jingglong'],
                    ['code' => '35.05.07.2006', 'name' => 'Bacem'],
                    ['code' => '35.05.07.2007', 'name' => 'Kaulon'],
                ],
            ],
            [
                'code' => '35.05.08',
                'name' => 'Nglegok',
                'villages' => [
                    ['code' => '35.05.08.1001', 'name' => 'Nglegok'],
                    ['code' => '35.05.08.2002', 'name' => 'Bangsri'],
                    ['code' => '35.05.08.2003', 'name' => 'Dayu'],
                    ['code' => '35.05.08.2004', 'name' => 'Jiwut'],
                    ['code' => '35.05.08.2005', 'name' => 'Kedawung'],
                    ['code' => '35.05.08.2006', 'name' => 'Kemloko'],
                    ['code' => '35.05.08.2007', 'name' => 'Penataran'],
                    ['code' => '35.05.08.2008', 'name' => 'Sumberasri'],
                ],
            ],
        ];

        foreach ($districts as $item) {
            $district = District::updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name']]
            );

            foreach ($item['villages'] as $v) {
                Village::updateOrCreate(
                    ['code' => $v['code']],
                    [
                        'district_id' => $district->id,
                        'name' => $v['name'],
                    ]
                );
            }
        }
    }
}
