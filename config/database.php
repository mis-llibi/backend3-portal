<?php

use Illuminate\Support\Str;

return [

  /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

  'default' => env('DB_CONNECTION', 'mysql'),

  /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

  'connections' => [

    'sqlite' => [
      'driver' => 'sqlite',
      'url' => env('DATABASE_URL'),
      'database' => env('DB_DATABASE', database_path('database.sqlite')),
      'prefix' => '',
      'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    ],

    'mysql' => [
      'driver' => 'mysql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST', '127.0.0.1'),
      'port' => env('DB_PORT', '3306'),
      'database' => env('DB_DATABASE', 'forge'),
      'username' => env('DB_USERNAME', 'forge'),
      'password' => env('DB_PASSWORD', ''),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
      'engine' => null,
      'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
      ]) : [],
    ],

    'mysql_sync' => [
      'driver' => 'mysql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST_SYNC', '127.0.0.1'),
      'port' => env('DB_PORT_SYNC', '3306'),
      'database' => env('DB_DATABASE_SYNC', 'forge'),
      'username' => env('DB_USERNAME_SYNC', 'forge'),
      'password' => env('DB_PASSWORD_SYNC', ''),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
      'engine' => null,
      'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
      ]) : [],
    ],

    'mysql_corporate' => [
      'driver' => 'mysql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST_CORPORATE', '127.0.0.1'),
      'port' => env('DB_PORT_CORPORATE', '3306'),
      'database' => env('DB_DATABASE_CORPORATE', 'forge'),
      'username' => env('DB_USERNAME_CORPORATE', 'forge'),
      'password' => env('DB_PASSWORD_CORPORATE', ''),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
      'engine' => null,
      'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
      ]) : [],
    ],

    'mysql_appcode' => [
      'driver' => 'mysql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST_APPCODE', '127.0.0.1'),
      'port' => env('DB_PORT_APPCODE', '3306'),
      'database' => env('DB_DATABASE_APPCODE', 'forge'),
      'username' => env('DB_USERNAME_APPCODE', 'forge'),
      'password' => env('DB_PASSWORD_APPCODE', ''),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
      'engine' => null,
      'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
      ]) : [],
    ],

    'mysql_pre_approve' => [
      'driver' => 'mysql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST_PRE_APPROVE', '127.0.0.1'),
      'port' => env('DB_PORT_PRE_APPROVE', '3306'),
      'database' => env('DB_DATABASE_PRE_APPROVE', 'forge'),
      'username' => env('DB_USERNAME_PRE_APPROVE', 'forge'),
      'password' => env('DB_PASSWORD_PRE_APPROVE', ''),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
      'engine' => null,
      'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
      ]) : [],
    ],

    'mysql_healthdash' => [
      'driver' => 'mysql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST_HEALTHDASH', '127.0.0.1'),
      'port' => env('DB_PORT_HEALTHDASH', '3306'),
      'database' => env('DB_DATABASE_HEALTHDASH', 'forge'),
      'username' => env('DB_USERNAME_HEALTHDASH', 'forge'),
      'password' => env('DB_PASSWORD_HEALTHDASH', ''),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
      'engine' => null,
      'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
      ]) : [],
    ],

    'pgsql' => [
      'driver' => 'pgsql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST', '127.0.0.1'),
      'port' => env('DB_PORT', '5432'),
      'database' => env('DB_DATABASE', 'forge'),
      'username' => env('DB_USERNAME', 'forge'),
      'password' => env('DB_PASSWORD', ''),
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
      // 'encrypt' => env('DB_ENCRYPT', 'yes'),
      // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
    ],

  ],

  /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

  'migrations' => 'migrations',

  /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

  'redis' => [

    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
      'cluster' => env('REDIS_CLUSTER', 'redis'),
      'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
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
