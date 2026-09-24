<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $linjamsos = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $rehsos = WorkUnit::where('name', 'like', '%Rehsos%')->first();
        $sekretariat = WorkUnit::where('name', 'like', '%Sekretariat%')->first();

        $kanigoroDistrict = District::where('name', 'Kanigoro')->first();
        $satreyanVillage = Village::where('name', 'Satreyan')->first();

        $defaultPassword = Hash::make('password');

        $users = [
            [
                'name' => 'Administrator Sistem Dinsos',
                'email' => 'admin@dinsos.blitarkab.go.id',
                'password' => $defaultPassword,
                'phone' => '081234567890',
                'nik' => '3505010101850001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Petugas Pelayanan DTSEN & PBI (Linjamsos)',
                'email' => 'petugas.linjamsos@dinsos.blitarkab.go.id',
                'password' => $defaultPassword,
                'phone' => '081234567891',
                'nik' => '3505010202900002',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Petugas Pelayanan Rehsos (Pekerja Sosial)',
                'email' => 'petugas.rehsos@dinsos.blitarkab.go.id',
                'password' => $defaultPassword,
                'phone' => '081234567892',
                'nik' => '3505010303920003',
                'work_unit_id' => $rehsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Kepala Bidang Linjamsos (Pejabat Paraf)',
                'email' => 'kabid.linjamsos@dinsos.blitarkab.go.id',
                'password' => $defaultPassword,
                'phone' => '081234567893',
                'nik' => '3505010404780004',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Kepala Dinas Sosial (Pejabat TTD & Pimpinan)',
                'email' => 'kadis@dinsos.blitarkab.go.id',
                'password' => $defaultPassword,
                'phone' => '081234567894',
                'nik' => '3505010505750005',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Operator Kecamatan Kanigoro',
                'email' => 'operator.kanigoro@blitarkab.go.id',
                'password' => $defaultPassword,
                'phone' => '081234567895',
                'nik' => '3505010606880006',
                'work_unit_id' => null,
                'district_id' => $kanigoroDistrict?->id,
                'village_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Operator Desa Satreyan / Puskesos',
                'email' => 'operator.satreyan@blitarkab.go.id',
                'password' => $defaultPassword,
                'phone' => '081234567896',
                'nik' => '3505010707950007',
                'work_unit_id' => null,
                'district_id' => $kanigoroDistrict?->id,
                'village_id' => $satreyanVillage?->id,
                'is_active' => true,
            ],
            [
                'name' => 'Budi Santoso (Masyarakat / Warga)',
                'email' => 'masyarakat@gmail.com',
                'password' => $defaultPassword,
                'phone' => '081298765432',
                'nik' => '3505011010900008',
                'work_unit_id' => null,
                'district_id' => $kanigoroDistrict?->id,
                'village_id' => $satreyanVillage?->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
