<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'administrator',
            'petugas_dinsos',
            'pejabat_penandatangan',
            'pimpinan',
            'operator_wilayah',
            'masyarakat',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Assign roles to seeded users
        $userRoleMap = [
            'admin@dinsos.blitarkab.go.id' => 'administrator',
            'petugas.linjamsos@dinsos.blitarkab.go.id' => 'petugas_dinsos',
            'petugas.rehsos@dinsos.blitarkab.go.id' => 'petugas_dinsos',
            'kabid.linjamsos@dinsos.blitarkab.go.id' => 'pejabat_penandatangan',
            'kadis@dinsos.blitarkab.go.id' => 'pimpinan',
            'operator.kanigoro@blitarkab.go.id' => 'operator_wilayah',
            'operator.satreyan@blitarkab.go.id' => 'operator_wilayah',
            'masyarakat@gmail.com' => 'masyarakat',
        ];

        foreach ($userRoleMap as $email => $role) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->syncRoles([$role]);
            }
        }
    }
}
