<?php

declare(strict_types=1);

namespace App\Business\Amqp\Consumer;

use App\Business\Services\IndexService;
use App\Business\Services\SyncService;
use App\Utils\Logger;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'publish-exchange', routingKey: 'publish-key', queue: 'publish-queue', name: "PublishConsumer", nums: 1)]
final class PublishConsumer extends ConsumerMessage
{
    #[Inject]
    private Redis $redis;

    private array $cmd = ['sync' => SyncService::class, 'flush' => IndexService::class];

    /**
     * Array
     * (
     * [uploadUrl] => /resources/230707/2OnrrmQM75D1.webp
     * [publishNewNamePath] => /resources/230707/2OnrrmQM75D1.js
     * )
     */
    public function consumeMessage($data, AMQPMessage $message): string
    {
        if (!isset($data['func'])) {
            Logger::alert([$data, '缺少参数func，drop此消息']);
            return Result::DROP;
        }

        try {
            $obj = $this->container->get($this->cmd[$data['func']]);
            $r   = $obj->{$data['func']}($data);
            if (true === $r) {
                return Result::ACK;
            }

            sleep(5);
            return Result::NACK;
        } catch (\Throwable $e) {
            Logger::error($e);
            return Result::DROP;
        }
    }
}
