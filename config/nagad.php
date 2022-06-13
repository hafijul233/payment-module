<?php

/**
 * Test Api Configuration
 */
return [
    'mode' => 'live', //sandbox , live

    'live' => [
        'endpoint' => 'http://api.hafijulislam.me/api',
        'username' => '7106UAT',
        'password' => '7106@Pass',
        'excode' => '7106',
    ],

    'sandbox' => [
        'endpoint' => 'http://api.hafijulislam.me/api',
        'username' => '7106UAT',
        'password' => '7106@Pass',
        'excode' => '7106',
    ],

    'header' => [
        'accept_type' => 'application/json', //[application/json, application/xml, text/html, text/plain, */*]
        'content_type' => '*/*', //[application/json, application/xml, text/html, text/plain, */*]
    ],
];
