<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Les admins peuvent voir toutes les commandes.
     * Les clients ne voient que celles de leur entreprise.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->client_id === $order->client_id;
    }

    /**
     * Seuls les clients authentifiés peuvent créer des commandes.
     */
    public function create(User $user): bool
    {
        return $user->client_id !== null;
    }

    /**
     * Seuls les admins peuvent mettre à jour le statut d'une commande.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Un client peut répéter uniquement ses propres commandes.
     */
    public function repeat(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->client_id === $order->client_id;
    }
}
