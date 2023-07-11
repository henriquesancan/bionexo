<?php

return [
    'driver' => env('DUSK_DRIVER', 'chrome'),

    'drivers' => [
        'chrome' => [
            'driver' => 'chrome',
            'port' => 4444,
            'host' => 'chrome',
            'path' => '/wd/hub',
            'chrome_options' => [

            ],
        ],
    ],
];
