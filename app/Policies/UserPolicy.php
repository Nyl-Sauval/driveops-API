<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Vérifie si un utilisateur peut voir la liste complète des utilisateurs.
     * Réservé aux admins.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Vérifie si un utilisateur peut voir un utilisateur spécifique.
     * Autorisé pour l’admin ou l’utilisateur lui-même.
     */
    public function view(User $user, User $targetUser): bool
    {
        return $user->role === User::ROLE_ADMIN || $user->id === $targetUser->id;
    }

    /**
     * Vérifie si un utilisateur peut voir les véhicules d’un autre utilisateur.
     * Autorisé pour l’admin ou l’utilisateur lui-même.
     */
    public function viewVehicles(User $user, User $targetUser): bool
    {
        return $user->role === User::ROLE_ADMIN || $user->id === $targetUser->id;
    }

    /**
     * Vérifie si un utilisateur peut mettre à jour un autre utilisateur.
     * Autorisé pour l’admin ou l’utilisateur lui-même.
     */
    public function update(User $user, User $targetUser): bool
    {
        return $user->role === User::ROLE_ADMIN || $user->id === $targetUser->id;
    }

    /**
     * Vérifie si un utilisateur peut supprimer un autre utilisateur.
     * Réservé aux admins.
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Vérifie si un utilisateur peut restaurer un autre utilisateur.
     * Réservé aux admins.
     */
    public function restore(User $user, User $targetUser): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Vérifie si un utilisateur peut supprimer définitivement un autre utilisateur.
     * Réservé aux admins.
     */
    public function forceDelete(User $user, User $targetUser): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
