<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan', 'operator_wilayah']);
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan', 'operator_wilayah']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->hasRole('administrator');
    }
}
