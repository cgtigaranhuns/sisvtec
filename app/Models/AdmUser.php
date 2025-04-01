<?php

namespace App\Models;

use LdapRecord\Models\Model;

class AdmUser extends Model
{
    protected string $guidKey = 'objectguid'; // Declarado explicitamente como string
    
    public static function boot():  void
    {
        parent::boot();
        
        static::addGlobalScope('users', function ($query) {
            $query->where('objectclass', '=', 'user');
        });
    }
}