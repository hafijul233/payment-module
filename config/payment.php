<?php
return [
    'driver' => 'guzzle',
    'drivers' => [
        'curl' => \HishabKitab\Payment\Driver\CurlRequest::class,
    ],
    'vendor' => 'test',
    'vendors' => [
        'test' => \HishabKitab\Payment\Vendors\Test::class
    ],
];
