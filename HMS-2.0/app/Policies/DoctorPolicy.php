<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;

class DoctorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Doctor $doctor): bool
    {
        return $user->hasRole(['admin', 'staff']) &&
            $user->business_id === $doctor->business_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Doctor $doctor): bool
    {
        return $user->hasRole('admin') &&
            $user->business_id === $doctor->business_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->hasRole('admin') &&
            $user->business_id === $doctor->business_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Doctor $doctor): bool
    {
        return $user->hasRole('admin') &&
            $user->business_id === $doctor->business_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Doctor $doctor): bool
    {
        return $user->hasRole('admin') &&
            $user->business_id === $doctor->business_id;
    }
}
