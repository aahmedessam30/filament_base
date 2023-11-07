<?php

/*
|--------------------------------------------------------------------------
| API Versions
|--------------------------------------------------------------------------
|
| Here you may specify the API versions that you want to use.
|
*/
return [
    'debug'           => env('API_DEBUG', true),
    'current_version' => env('API_VERSION'),
    'default_files'   => [
        'middlewares' => ['api'],
        'files'       => [
            ['name' => 'auth', 'prefix'  => 'auth', 'as' => 'auth']
        ],
    ],
    'versions'        => [
        'v1' => [
            'name'        => 'v1',
            'description' => 'First version of the API',
            'status'      => strtolower(env('API_VERSION')) === 'v1' ? 'active' : 'inactive',
            'date'        => '06-11-2023',
            'middlewares' => ['api'],
            'files'       => [],
        ],
    ],
];
