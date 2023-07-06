<?php

declare(strict_types=1);

return [
    'default' => [
        'driver' => Hyperf\Cache\Driver\RedisDriver::class,
        // 'packer' => Hyperf\Codec\Packer\PhpSerializerPacker::class,
        'packer' => \App\Utils\Packer::class,
        'prefix' => 'c:',
    ],
    'co'      => [
        'driver' => Hyperf\Cache\Driver\CoroutineMemoryDriver::class,
        'packer' => \App\Utils\Packer::class,
    ],
];
