<?php

declare(strict_types=1);

return [
    'default' => [
        'handler'   => [
            'class'       => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => RUNTIME_PATH . '/logs/' . date('Y-m-d') . '.log',
                'level'    => Monolog\Level::Debug,
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
