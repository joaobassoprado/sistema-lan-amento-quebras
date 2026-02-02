<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        /*
        |--------------------------------------------------------------------------
        | BANCO LOCAL (QUEBRAS - DOCKER)
        |--------------------------------------------------------------------------
        */
        'mysql' => [
            'driver'      => 'mysql',
            'url'         => env('DATABASE_URL'),
            'host'        => env('DB_HOST', 'mysql'),   // ← CORRIGIDO AQUI
            'port'        => env('DB_PORT', '3306'),
            'database'    => env('DB_DATABASE', 'quebras_v2'),
            'username'    => env('DB_USERNAME', 'dev'),
            'password'    => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'prefix_indexes' => true,
            'strict'      => true,
            'engine'      => null,
            'options'     => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | BANCO 200 - PRODUÇÃO (APENAS LEITURA)
        |--------------------------------------------------------------------------
        */
        'db200' => [
    'driver'    => 'mysql', // Ou env('DB200_CONNECTION', 'mysql')
    'host'      => env('DB200_HOST', '10.4.0.200'),
    'port'      => env('DB200_PORT', '3306'),
    'database'  => env('DB200_DATABASE', 'bases'),
    'username'  => env('DB200_USERNAME', 'mapeamento_trajeto'),
    'password'  => env('DB200_PASSWORD', '@Gm081223'),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => false,
    'engine'    => null,
],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DW_HOST', '127.0.0.1'),
            'port' => env('DW_PORT', '5432'),
            'database' => env('DW_DATABASE', 'forge'),
            'username' => env('DW_USERNAME', 'forge'),
            'password' => env('DW_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],
    ],

    'migrations' => 'migrations',

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env(
                'REDIS_PREFIX',
                Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'
            ),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],

];
