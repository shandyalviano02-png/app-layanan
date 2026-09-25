<?php

namespace App\Policies;

use App\Models\RehabilitationCase;
use App\Models\User;

class RehabilitationCasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan']);
    }

    public function view(User $user, RehabilitationCase $case): bool
    {
        if ($user->hasRole('administrator') || $user->hasRole('pimpinan')) {
            return true;
        }

        if ($user->hasRole('petugas_dinsos')) {
            return $user->id === $case->officer_id || is_null($case->officer_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, RehabilitationCase $case): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        if ($user->hasRole('administrator')) {
            return true;
        }

        return $user->hasRole('petugas_dinsos') && ($user->id === $case->officer_id || is_null($case->officer_id));
    }

    public function delete(User $user, RehabilitationCase $case): bool
    {
        return $user->hasRole('administrator');
    }
}
