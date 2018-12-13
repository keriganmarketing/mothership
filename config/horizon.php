<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:default' => 60,
    ],

    'trim' => [
        'recent' => 5040,
        'failed' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'environments' => [
        'production' => [
            'main-supervisor' => [
                'connection' => 'main',
                'queue' => ['main'],
                'balance' => 'auto',
                'processes' => 6,
                'timeout' => 100,
                'tries' => 1,
            ],
            'updater-supervisor' => [
                'connection' => 'updaters',
                'queue' => ['updaters'],
                'balance' => 'auto',
                'processes' => 2,
                'timeout' => 600,
                'tries' => 1,
            ],
            'cleaner-supervisor' => [
                'connection' => 'cleaners',
                'queue' => ['cleaners'],
                'balance' => 'auto',
                'processes' => 2,
                'timeout' => 1200,
                'tries' => 1,
            ],
        ],

        'local' => [
            'main-supervisor' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 1,
            ],
        ],
    ],
];
