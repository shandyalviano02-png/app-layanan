<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan']);
    }

    public function view(User $user, Complaint $complaint): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, Complaint $complaint): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('administrator');
    }
}
