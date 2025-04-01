<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default LDAP Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the LDAP connections below you wish
    | to use as your default connection for all LDAP operations. Of
    | course you may add as many connections you'd like below.
    |
    */

   'default' => env('LDAP_CONNECTION', 'adm'),

    /*
    |--------------------------------------------------------------------------
    | LDAP Connections
    |--------------------------------------------------------------------------
    |
    | Below you may configure each LDAP connection your application requires
    | access to. Be sure to include a valid base DN - otherwise you may
    | not receive any results when performing LDAP search operations.
    |
    */

    'connections' => [
        'adm' => [
            'hosts' => ['172.28.1.77'],
            'username' => env('LDAP_ADM_USERNAME'),
            'password' => env('LDAP_ADM_PASSWORD'),
            'port' => env('LDAP_ADM_PORT', 389),
            'base_dn' => env('LDAP_ADM_BASE_DN', 'dc=adm,dc=garanhuns,dc=ifpe'),
            'timeout' => env('LDAP_ADM_TIMEOUT', 5),
            'use_ssl' => env('LDAP_ADM_SSL', false),
            'use_tls' => env('LDAP_ADM_TLS', false),
    ],
    'labs' => [
        'hosts' => ['172.28.2.55'],
        'username' => env('LDAP_LABS_USERNAME'),
        'password' => env('LDAP_LABS_PASSWORD'),
        'port' => env('LDAP_LABS_PORT', 389),
        'base_dn' => env('LDAP_LABS_BASE_DN', 'dc=labs,dc=garanhuns,dc=ifpe'),
        'timeout' => env('LDAP_LABS_TIMEOUT', 5),
        'use_ssl' => env('LDAP_LABS_SSL', false),
        'use_tls' => env('LDAP_LABS_TLS', false),
    ],
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Logging
    |--------------------------------------------------------------------------
    |
    | When LDAP logging is enabled, all LDAP search and authentication
    | operations are logged using the default application logging
    | driver. This can assist in debugging issues and more.
    |
    */

    'logging' => [
        'enabled' => env('LDAP_LOGGING', true),
        'channel' => env('LOG_CHANNEL', 'stack'),
        'level' => env('LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Cache
    |--------------------------------------------------------------------------
    |
    | LDAP caching enables the ability of caching search results using the
    | query builder. This is great for running expensive operations that
    | may take many seconds to complete, such as a pagination request.
    |
    */

    'cache' => [
        'enabled' => env('LDAP_CACHE', false),
        'driver' => env('CACHE_DRIVER', 'file'),
    ],

];
