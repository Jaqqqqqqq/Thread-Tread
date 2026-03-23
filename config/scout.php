<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search "driver" that will be used by
    | Laravel Scout. You may set this to any of the following drivers:
    | "algolia", "database", "meilisearch", "null", "typesense"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names created by Scout. This prefix may be used to avoid collisions
    | when you have multiple "Scout" installations in the same application.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that sync your data
    | with your search indexes are queued. When this is set to "true" then
    | all automatic data syncing will get queued for better performance.
    |
    */

    'queue' => env('SCOUT_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Below you may specify the database connection that should be used
    | when syncing model information to the search index.
    |
    */

    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
    ],

];
