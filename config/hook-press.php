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
            'path' => 'bootstrap/cache/hookpress.php',
        ],
        'cache' => [
            'store' => null,  // null = default cache store
            'key' => 'hookpress:map',
            'ttl' => null,    // forever
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer hooks
    |--------------------------------------------------------------------------
    | Which events should trigger a rebuild.
    */
    'composer' => [
        'on' => ['post-install-cmd', 'post-update-cmd'],
    ],
];
