<?php

namespace App\Policies;

// use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use Spatie\Permission\Models\Role;

class FuncaoPolicy
{
    use HandlesAuthorization;

   
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Funcao');
    }

   
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('Ver Funcao');
    }

    
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Funcao');
    }

    
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('Editar Funcao');
    }

   
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('Excluir Funcao');
    }

    
    public function restore(User $user, Role $role)
    {
        //
    }

   
    public function forceDelete(User $user, Role $role)
    {
        //
    }
}
