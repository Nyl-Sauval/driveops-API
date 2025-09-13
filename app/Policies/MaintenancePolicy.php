<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MaintenancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admin users can view any maintenance records
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Maintenance $maintenance): bool
    {
        // Admin users can view any maintenance record
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Check if the user is associated with the maintenance record
        return $maintenance->vehicles()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Maintenance $maintenance): bool
    {
        // Only admin or the user associated with the maintenance can update it
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Check if the user is associated with the maintenance record
        return $maintenance->vehicles()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Maintenance $maintenance): bool
    {
        // Only admin or the user associated with the maintenance can delete it
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Check if the user is associated with the maintenance record
        return $maintenance->vehicles()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Maintenance $maintenance): bool
    {
        // Only admin users can restore maintenance records
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Maintenance $maintenance): bool
    {
        // Only admin users can permanently delete maintenance records
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can view the maintenance records of a specific user.
     */
    public function viewUserMaintenance(User $user, User $targetUser): bool
    {
        // Admin users can view any user's maintenance records
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Users can only view their own maintenance records
        return $user->id === $targetUser->id;
    }

    /**
     * Determine whether the user can view the maintenance records of a specific vehicle.
     */
    public function viewVehiculeMaintenance(User $user, Maintenance $maintenance): bool
    {
        // Admin users can view any vehicle's maintenance records
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Check if the user is associated with the vehicle linked to the maintenance record
        return $maintenance->vehicles()->where('user_id', $user->id)->exists();
    }
}
