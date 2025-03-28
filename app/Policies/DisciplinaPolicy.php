<?php

namespace App\Policies;

use App\Models\Disciplina;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class DisciplinaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Disciplina');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Disciplina $disciplina): bool
    {
        return $user->hasPermissionTo('Ver Disciplina');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Disciplina');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Disciplina $disciplina): bool
    {
        return $user->hasPermissionTo('Editar Disciplina');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Disciplina $disciplina): bool
    {
        return $user->hasPermissionTo('Excluir Disciplina');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Disciplina $disciplina)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Disciplina $disciplina)
    {
        //
    }
}
