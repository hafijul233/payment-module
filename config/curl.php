<?php

return [
    /**
     * The default setting values
     *
     * @var array
     */
    'timeout' => 0.0,
    'connect_timeout' => 150,
    'debug' => false,
    'verify' => true,
    'version' => null, //[1.0, 1.1]
    'http_errors' => true,
    /**
     * The number of milliseconds to delay before
     * sending the request.
     *
     * @var float
     */
    'delay' => 0.0,
    /**
     * Default values for when 'allow_redirects'
     * option is true.
     *
     * @var array
     */
    'allow_redirects' => true,
    'redirect' => [
        'max' => 5,
        'strict' => true,
        'protocols' => [
            'http',
            'https',
        ],
    ],
];
