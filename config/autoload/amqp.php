<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'default' => [
        'host'       => env('AMQP_HOST'),
        'port'       => (int)env('AMQP_PORT'),
        'user'       => env('AMQP_USER'),
        'password'   => env('AMQP_PASSWORD'),
        'vhost'      => env('AMQP_VHOST'),
        'concurrent' => [
            'limit' => 1,
        ],
        'pool'       => [
            'connections' => 1,
        ],
        'params'     => [
            'insist'             => false,
            'login_method'       => 'AMQPLAIN',
            'login_response'     => null,
            'locale'             => 'en_US',
            'connection_timeout' => 3.0,
            'read_write_timeout' => 9.0,
            'context'            => null,
            'keepalive'          => false,
            'heartbeat'          => 3,
            'close_on_destruct'  => false,
        ],
    ],
    // 'pool'   => [],
];
