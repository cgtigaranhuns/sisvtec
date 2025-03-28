<?php

namespace App\Policies;

use App\Models\Curso;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class CursoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Curso');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Curso $curso): bool
    {
        return $user->hasPermissionTo('Ver Curso');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Curso');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Curso $curso): bool
    {
        return $user->hasPermissionTo('Editar Curso');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Curso $curso): bool
    {
        return $user->hasPermissionTo('Excluir Curso');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Curso $curso)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Curso $curso)
    {
        //
    }
}
