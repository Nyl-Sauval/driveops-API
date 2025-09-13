<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Auth\Access\Response;

class VehiculePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow only admin users to view any vehicles
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vehicule $vehicule): bool
    {
        // Allow only admin or owner users to view the vehicle
        return $user->role === User::ROLE_ADMIN || $user->id === $vehicule->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vehicule $vehicule): bool
    {
        // Allow only admin or owner users to update the vehicle
        return $user->role === User::ROLE_ADMIN || $user->id === $vehicule->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicule $vehicule): bool
    {
        // Allow only admin or owner users to delete the vehicle
        return $user->role === User::ROLE_ADMIN || $user->id === $vehicule->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vehicule $vehicule): bool
    {
        // Allow only admin users to restore the vehicle
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vehicule $vehicule): bool
    {
        // Allow only admin users to permanently delete the vehicle
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can view the vehicles of a specific user.
     */
    public function viewUserVehicles(User $user, User $targetUser): bool
    {
        // Allow only admin or the user themselves to view their own vehicles
        return $user->role === User::ROLE_ADMIN || $user->id === $targetUser->id;
    }
}
