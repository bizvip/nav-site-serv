<?php

declare(strict_types=1);

use Hyperf\Server\Event;
use Hyperf\Server\ServerInterface;
use Swoole\Constant;

return [
    'mode'      => SWOOLE_PROCESS,
    'servers'   => [
        [
            'name'      => 'http',
            'type'      => ServerInterface::SERVER_HTTP,
            'host'      => '127.0.0.1',
            'port'      => 8818,
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                Event::ON_REQUEST => [Hyperf\HttpServer\Server::class, 'onRequest'],
            ],
        ],
    ],
    'settings'  => [
        Constant::OPTION_DOCUMENT_ROOT          => PUBLIC_PATH,
        Constant::OPTION_ENABLE_STATIC_HANDLER  => true,
        Constant::OPTION_HTTP_AUTOINDEX         => false,
        Constant::OPTION_HTTP_INDEX_FILES       => ['index.html'],
        Constant::OPTION_UPLOAD_TMP_DIR         => RUNTIME_PATH.'/uploads',
        Constant::OPTION_HTTP_COMPRESSION       => true,
        Constant::OPTION_HTTP_COMPRESSION_LEVEL => 2,
        Constant::OPTION_ENABLE_COROUTINE       => true,
        Constant::OPTION_WORKER_NUM             => swoole_cpu_num(),
        Constant::OPTION_PID_FILE               => RUNTIME_PATH.'/'.\App\Utils\Str::snake(env('APP_NAME'), '-').'.pid',
        Constant::OPTION_OPEN_TCP_NODELAY       => true,
        Constant::OPTION_MAX_COROUTINE          => 100000,
        Constant::OPTION_OPEN_HTTP2_PROTOCOL    => true,
        Constant::OPTION_MAX_REQUEST            => 200000,
        Constant::OPTION_SOCKET_BUFFER_SIZE     => 2 * 1024 * 1024,
        Constant::OPTION_BUFFER_OUTPUT_SIZE     => 2 * 1024 * 1024,
    ],
    'callbacks' => [
        Event::ON_WORKER_START => [
            Hyperf\Framework\Bootstrap\WorkerStartCallback::class,
            'onWorkerStart',
        ],
        Event::ON_PIPE_MESSAGE => [
            Hyperf\Framework\Bootstrap\PipeMessageCallback::class,
            'onPipeMessage',
        ],
        Event::ON_WORKER_EXIT  => [
            Hyperf\Framework\Bootstrap\WorkerExitCallback::class,
            'onWorkerExit',
        ],
    ],
];
