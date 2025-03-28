<?php

namespace App\Policies;

use App\Models\Banco;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class BancoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Banco');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Banco $banco): bool
    {
        return $user->hasPermissionTo('Ver Banco');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Banco');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Banco $banco): bool
    {
        return $user->hasPermissionTo('Editar Banco');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Banco $banco): bool
    {
        return $user->hasPermissionTo('Excluir Banco');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Banco $banco)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Banco $banco)
    {
        //
    }
}
