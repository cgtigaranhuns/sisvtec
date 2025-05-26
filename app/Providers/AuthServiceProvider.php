<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Policies\FuncaoPolicy;
use App\Policies\UsuarioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use App\Policies\ActivityPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UsuarioPolicy::class,
        Role::class => FuncaoPolicy::class,  
        Permission::class => FuncaoPolicy::class,
         Activity::class => ActivityPolicy::class,
        
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::provider('multi-ldap', function ($app, array $config) {
            return new MultiLdapUserProvider();
        });
    }
}
