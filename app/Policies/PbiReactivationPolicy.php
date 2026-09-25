<?php

namespace App\Policies;

use App\Models\PbiReactivation;
use App\Models\User;

class PbiReactivationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pejabat_penandatangan', 'pimpinan', 'operator_wilayah']);
    }

    public function view(User $user, PbiReactivation $pbi): bool
    {
        if ($user->hasRole('administrator') || $user->hasRole('petugas_dinsos') || $user->hasRole('pejabat_penandatangan') || $user->hasRole('pimpinan')) {
            return true;
        }

        if ($user->hasRole('operator_wilayah') && $pbi->serviceRequest) {
            $req = $pbi->serviceRequest;
            if ($user->village_id && $req->village_id) {
                return $user->village_id === $req->village_id;
            }
            if ($user->district_id && $req->district_id) {
                return $user->district_id === $req->district_id;
            }
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, PbiReactivation $pbi): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pejabat_penandatangan']);
    }

    public function delete(User $user, PbiReactivation $pbi): bool
    {
        return $user->hasRole('administrator');
    }
}
