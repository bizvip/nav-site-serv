<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'publish-exchange', routingKey: 'publish-key', queue: 'publish-queue', name: "PublishConsumer", nums: 1)]
final class PublishConsumer extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): string
    {
        print_r($data);
        return Result::ACK;
    }
}