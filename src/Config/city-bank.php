<?php
/**
 * Created by PhpStorm.
 * User: MD ARIFUL HAQUE
 * Date: 7/10/2020
 * Time: 1:00 AM
 */


return [
    'mode'  => env('CITY_BANK_API_MODE','sandbox'),
    'sandbox' => [
        'username'              => env( 'CITY_BANK_API_USERNAME', 'cbl_mycash_online'),
        'password'              => env( 'CITY_BANK_API_PASSWORD', 'Myash0nlin3'),
        'exchange_company'      => env('CITY_BANK_EXCHANGE_COMPANY', 'MyCash Online'),
        'app_host'              => env('CITY_BANK_API_HOST', 'nrbms.thecitybank.com'),
    ],
    'live' => [
        'username'              => env( 'CITY_BANK_API_USERNAME', 'mycash_online_prod'),
        'password'              => env( 'CITY_BANK_API_PASSWORD', 'MyashIwcbl2020'),
        'exchange_company'      => env('CITY_BANK_EXCHANGE_COMPANY', 'MyCash Online'),
        'app_host'              => env('CITY_BANK_API_HOST', 'nrbms.thecitybank.com'),
    ],
];
