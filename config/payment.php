<?php

return [
    'driver' => 'curl',
    'drivers' => [
        'curl' => \HishabKitab\Payment\Driver\Curl::class,
    ],
    'vendor' => 'test',
    'vendors' => [
        'test' => \HishabKitab\Payment\Vendors\Test::class,
    ],
];
