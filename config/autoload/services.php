<?php

declare(strict_types=1);

return [
    'consumers' => [
        [
            'name'          => 'PublishService',
            'service'       => \App\Business\Rpc\PublishServiceInterface::class,
            'id'            => \App\Business\Rpc\PublishServiceInterface::class,
            'protocol'      => 'jsonrpc-http',
            'load_balancer' => 'random',
            // 'registry'      => [
            //     'protocol' => 'consul',
            //     'address'  => 'http://127.0.0.1:8500',
            // ],
            // 如果没有指定上面的 registry 配置，即为直接对指定的节点进行消费，通过下面的 nodes 参数来配置服务提供者的节点信息
            'nodes'         => [
                ['host' => \Hyperf\Support\env('RPC_SERV_1'), 'port' => 18021],
            ],
            // 配置项，会影响到 Packer 和 Transporter
            'options'       => [
                'connect_timeout' => 10.0,
                'recv_timeout'    => 10.0,
                // 'settings'        => [
                // 根据协议不同，区分配置
                // 'open_eof_split' => true,
                // 'package_eof'    => "\r\n",
                // 'open_length_check' => true,
                // 'package_length_type' => 'N',
                // 'package_length_offset' => 0,
                // 'package_body_offset' => 4,
                // ],
                // 重试次数，默认值为 2，收包超时不进行重试。暂只支持 JsonRpcPoolTransporter
                'retry_count'     => 2,
                // 重试间隔，毫秒
                'retry_interval'  => 100,
                // 使用多路复用 RPC 时的心跳间隔，null 为不触发心跳
                'heartbeat'       => 100,
                // JsonRpcPoolTransporter
                'pool'            => [
                    'min_connections' => 1,
                    'max_connections' => 32,
                    'connect_timeout' => 60.0,
                    'wait_timeout'    => 10.0,
                    'heartbeat'       => -1,
                    'max_idle_time'   => 120.0,
                ],
            ],
        ],
    ],
];
