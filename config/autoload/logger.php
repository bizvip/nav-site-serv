<?php

declare(strict_types=1);

return [
    'default' => [
        'handler'   => [
            'class'       => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => RUNTIME_PATH . '/logs/nav-site-serv.log',
                'level'  => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class'       => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format'                => null,
                'dateFormat'            => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
];
