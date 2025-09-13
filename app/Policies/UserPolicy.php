<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserPolicy
{
    /**
     * Vérifie si un utilisateur peut voir les véhicules d’un autre utilisateur.
     */
    public function viewVehicles(User $user, User $targetUser): bool
    {
        // Autorisé si l'utilisateur est admin
        // ou si c'est l'utilisateur lui-même
        return $user->role === User::ROLE_ADMIN || $user->id === $targetUser->id;
    }
}
