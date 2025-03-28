<?php

namespace App\Policies;

use App\Models\Cargo;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class CargoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Cargo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cargo $cargo): bool
    {
        return $user->hasPermissionTo('Ver Cargo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Cargo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cargo $cargo): bool
    {
        return $user->hasPermissionTo('Editar Cargo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cargo $cargo): bool
    {
        return $user->hasPermissionTo('Excluir Cargo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cargo $cargo)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cargo $cargo)
    {
        //
    }
}
