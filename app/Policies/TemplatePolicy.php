<?php

namespace App\Policies;

use App\Models\Template;
use App\Models\User;

class TemplatePolicy
{
    /**
     * Les admins peuvent voir tous les templates.
     * Les clients ne voient que ceux de leur entreprise.
     */
    public function view(User $user, Template $template): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->client_id === $template->department->client_id;
    }

    /**
     * Seuls les admins peuvent créer des templates.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Seuls les admins peuvent modifier des templates.
     */
    public function update(User $user, Template $template): bool
    {
        return $user->isAdmin();
    }

    /**
     * Seuls les admins peuvent supprimer des templates.
     */
    public function delete(User $user, Template $template): bool
    {
        return $user->isAdmin();
    }
}
