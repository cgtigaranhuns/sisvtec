<?php

namespace App\Policies;


use App\Models\User;
use Spatie\Activitylog\Models\Activity as ModelsActivity;

class ActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Ver Activity');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ModelsActivity $activity)
    {
        return $user->hasPermissionTo('Criar Activity');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ModelsActivity $activity)
    {
       
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user,ModelsActivity $activity)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user,ModelsActivity $activity)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user,ModelsActivity $activity)
    {
        //
    }
}
