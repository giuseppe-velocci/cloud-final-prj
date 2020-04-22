<?php
return[
    'headers' => [
        'Access-Control-Allow-Origin' => 'http://localhost',
        'Content-Type' => 'application/json; charset=UTF-8',
        'Access-Control-Allow-Methods' => 'POST',
        'Access-Control-Max-Age' => '3600',
        'Access-Control-Allow-Headers' => 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
    ],
    'timezone' => 'Europe/Rome',
    'key' => "example_key",
    'iss' => "http://example.org",
    'aud' => "http://example.com",
    'iat' => 1356999524,
    'nbf' => 1357000000
];
?>