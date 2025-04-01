<?php

namespace App\Models;

use LdapRecord\Models\Model;

class LabsUser extends Model
{
    protected string $guidKey = 'objectguid';
    
    public static function boot(): void
    {
        parent::boot();
        
        static::addGlobalScope('users', function ($query) {
            $query->whereHas('objectclass', 'user');
        });
    }
}