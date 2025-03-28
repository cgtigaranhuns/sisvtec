<?php

namespace App\Policies;

use App\Models\Discente;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class DiscentePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Discente');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Discente $discente): bool
    {
        return $user->hasPermissionTo('Ver Discente');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Discente');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Discente $discente): bool
    {
        return $user->hasPermissionTo('Editar Discente');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Discente $discente): bool
    {
        return $user->hasPermissionTo('Excluir Discente');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Discente $discente)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Discente $discente)
    {
        //
    }
}
