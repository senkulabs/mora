<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | Default module namespace.
    |
    */
    'namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Module Paths
    |--------------------------------------------------------------------------
    */
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Modules path
        |--------------------------------------------------------------------------
        |
        | This path is used to save the generated module.
        |
        */
        'modules' => base_path('Modules'),

        /*
        |--------------------------------------------------------------------------
        | The app path
        |--------------------------------------------------------------------------
        |
        | app folder name
        |
        */
        'app_folder' => 'app/',

        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        */
        'generator' => [
            // app/
            'controller' => ['path' => 'app/Http/Controllers'],
            'model' => ['path' => 'app/Models'],
            'provider' => ['path' => 'app/Providers'],

            // database/
            'factory' => ['path' => 'database/factories'],
            'migration' => ['path' => 'database/migrations'],
            'seeder' => ['path' => 'database/seeders'],

            // resources/
            'views' => ['path' => 'resources/views'],

            // routes/
            'routes' => ['path' => 'routes'],

            // tests/
            'test-feature' => ['path' => 'tests/Feature'],
            'test-unit' => ['path' => 'tests/Unit'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Discover of Modules
    |--------------------------------------------------------------------------
    */
    'auto-discover' => [
        'migrations' => true,
        'translations' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned.
    |
    */
    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],

];
