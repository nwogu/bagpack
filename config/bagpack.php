<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Should Bagpack Run?
    |--------------------------------------------------------------------------
    |
    | Determine if bagpack should package migration files
    | Defaults to true
    |
    */

    'run' => true,

    /*
    |--------------------------------------------------------------------------
    | Migration File Path
    |--------------------------------------------------------------------------
    |
    | Set a migration file path
    | Defaults to database/migrations
    |
    */

    'path' => database_path('migrations'),
];