<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Namespaces to scan
    |--------------------------------------------------------------------------
    */
    'roots' => [
        'App\\',                    // default
        // 'Domain\\',               // add more roots if needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Trait discovery
    |--------------------------------------------------------------------------
    */
    'traits' => [
        'namespaces' => ['App\\Traits\\'],
        'group_key' => 'traits', // key under which trait->classes map is stored
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Maps (your "types")
    |--------------------------------------------------------------------------
    */
    'maps' => [
        'payout_methods' => [
            'namespaces' => ['App\\Classes\\PayoutMethods\\'],
            'conditions' => [
                'isInstantiable',
                'implementsInterface' => 'App\\Interfaces\\PayoutMethod',
                // 'usesTrait' => 'App\\Traits\\SomeTrait',
                // 'hasAttribute' => 'App\\Attributes\\Discoverable',
            ],
        ],
        // Find all Eloquent models (non-abstract)
        'models' => [
            'namespaces' => ['App\\Models\\'],
            'conditions' => [
                'extends' => \Illuminate\Database\Eloquent\Model::class,
                'isInstantiable',
            ],
        ],
        // Invokables (handlers with __invoke)
        'invokables' => [
            'namespaces' => ['App\\Actions\\', 'App\\Jobs\\'],
            'conditions' => [
                'isInstantiable',
                // method existence + visibility
                'hasMethod' => ['name' => '__invoke', 'public' => true],
            ],
        ],
        // Controllers (name ends with Controller)
        'controllers' => [
            'namespaces' => ['App\\Http\\Controllers\\'],
            'conditions' => [
                'isInstantiable',
                'nameMatches' => '/Controller$/',
            ],
        ],
        // Services that expose a specific API
        'drivers' => [
            'namespaces' => ['App\\Services\\'],
            'conditions' => [
                'isInstantiable',
                'hasMethod' => ['name' => 'handle', 'public' => true, 'returns' => 'void'],
                'hasProperty' => ['name' => 'driver', 'public' => true, 'type' => 'string'],
            ],
        ],

        // Final singletons / leaf implementations
        'final_services' => [
            'namespaces' => ['App\\Services\\'],
            'conditions' => [
                'isFinal',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclusions
    |--------------------------------------------------------------------------
    */
    'exclusions' => [
        'classes' => [
            // 'App\\Foo\\Bar',
        ],
        'namespaces' => [
            // 'App\\Experimental\\',
        ],
        'regex' => [
            // '/^App\\\\Legacy\\\\/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistence
    |--------------------------------------------------------------------------
    */
    'store' => [
        'driver' => 'file', // 'file' or 'cache'
        'file' => [
            'path' => 'bootstrap/cache/hook-press.php',
        ],
        'cache' => [
            'store' => null,  // null = default cache store
            'key' => 'hook-press:map',
            'ttl' => null,    // forever
        ],
    ],

    'composer' => [
        'artisan_command' => 'hook-press:build',
        // Scanner will read the classmap from here used only by HookPress.
        'classmap_path' => '/vendor/composer/autoload_classmap.php',
    ],

];
