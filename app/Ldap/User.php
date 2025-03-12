<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class User extends Model
{
    public ?string $connection = 'labs';

    public static array $objectClasses = [

    ];
}
