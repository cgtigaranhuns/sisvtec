<?php

namespace App\Policies;

use App\Models\SubCategoria;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class SubCategoriaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Subcategoria');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SubCategoria $subCategoria): bool
    {
        return $user->hasPermissionTo('Ver Subcategoria');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Subcategoria');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SubCategoria $subCategoria): bool
    {
        return $user->hasPermissionTo('Editar Subcategoria');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SubCategoria $subCategoria): bool
    {
        return $user->hasPermissionTo('Excluir Subcategoria');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SubCategoria $subCategoria)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SubCategoria $subCategoria)
    {
        //
    }
}
