<?php

namespace App\Policies;

use App\Models\Config;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class ConfigPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Config');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Config $config): bool
    {
        return $user->hasPermissionTo('Ver Config');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Config');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Config $config): bool
    {
        return $user->hasPermissionTo('Editar Config');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Config $config): bool
    {
        return $user->hasPermissionTo('Excluir Config');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Config $config)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Config $config)
    {
        //
    }
}
