<?php

namespace App\Policies;

use App\Models\Coordenacao;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class CoordenacaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Coordenacao');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Coordenacao $coordenacao): bool
    {
        return $user->hasPermissionTo('Ver Coordenacao');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Coordenacao');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Coordenacao $coordenacao): bool
    {
        return $user->hasPermissionTo('Editar Coordenacao');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Coordenacao $coordenacao): bool
    {
        return $user->hasPermissionTo('Excluir Coordenacao');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Coordenacao $coordenacao)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Coordenacao $coordenacao)
    {
        //
    }
}
