<?php

namespace App\Policies;

use App\Models\Categoria;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class CategoriaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Categoria');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Categoria $categoria): bool
    {
        return $user->hasPermissionTo('Ver Categoria');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Categoria');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Categoria $categoria): bool
    {
        return $user->hasPermissionTo('Editar Categoria');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Categoria $categoria): bool
    {
        return $user->hasPermissionTo('Excluir Categoria');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Categoria $categoria)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Categoria $categoria)
    {
        //
    }
}
