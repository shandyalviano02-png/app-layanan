<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pejabat_penandatangan', 'pimpinan', 'operator_wilayah']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->hasRole('administrator') || $user->hasRole('petugas_dinsos') || $user->hasRole('pejabat_penandatangan') || $user->hasRole('pimpinan')) {
            return true;
        }

        if ($user->hasRole('operator_wilayah')) {
            if ($user->village_id && $serviceRequest->village_id) {
                return $user->village_id === $serviceRequest->village_id;
            }
            if ($user->district_id && $serviceRequest->district_id) {
                return $user->district_id === $serviceRequest->district_id;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'operator_wilayah']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        if ($user->hasRole('administrator') || $user->hasRole('petugas_dinsos') || $user->hasRole('pejabat_penandatangan')) {
            return true;
        }

        if ($user->hasRole('operator_wilayah')) {
            $isSameLocation = false;
            if ($user->village_id && $serviceRequest->village_id) {
                $isSameLocation = ($user->village_id === $serviceRequest->village_id);
            } elseif ($user->district_id && $serviceRequest->district_id) {
                $isSameLocation = ($user->district_id === $serviceRequest->district_id);
            }

            return $isSameLocation && in_array($serviceRequest->status->value, ['draft', 'revision_requested', 'submitted']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('administrator');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('administrator');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('administrator');
    }
}
