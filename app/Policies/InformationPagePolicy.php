<?php

namespace App\Policies;

use App\Models\InformationPage;
use App\Models\User;

class InformationPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan', 'operator_wilayah']);
    }

    public function view(User $user, InformationPage $informationPage): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan', 'operator_wilayah']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, InformationPage $informationPage): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function delete(User $user, InformationPage $informationPage): bool
    {
        return $user->hasRole('administrator');
    }
}
