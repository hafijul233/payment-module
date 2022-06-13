<?php

return [
    'driver' => 'curl',
    'drivers' => [
        'curl' => \HishabKitab\Payment\Http\Driver\Curl::class,
        'guzzle' => \HishabKitab\Payment\Http\Driver\Guzzle::class,
    ],
    'vendor' => 'test',
    'vendors' => [
        'test' => \HishabKitab\Payment\Vendors\Test::class,
        'nagad' => \HishabKitab\Payment\Vendors\Nagad::class,
    ],
];
