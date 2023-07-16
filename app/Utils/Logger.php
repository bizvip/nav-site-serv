<?php

/******************************************************************************
 * Copyright (c) M3-1-1 AChang 2023.                                          *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Utils;

use DateTimeImmutable;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;

/**
 * @method static void error(array|int|string|\Stringable $logData, string $channel = null)
 * @method static void info(array|int|string|\Stringable $logData, string $channel = null)
 * @method static void debug(array|int|string|\Stringable $logData, string $channel = null)
 * @method static void alert(array|int|string|\Stringable $logData, string $channel = null)
 */
final class Logger
{
    public static function __callStatic($method, array $args)
    {
        if (isset($args[1])) {
            $channel = $args[1];
        } else if (isset($args['channel'])) {
            $channel = $args['channel'];
        } else {
            $channel = config('app_env');
        }

        if (isset($args[0])) {
            $text = $args[0];
        } else if (isset($args['logData'])) {
            $text = $args['logData'];
        } else {
            $text = 'Logger 没有记录到任何错误消息';
        }

        $text = is_array($text) ? JSON::encode($text) : (string)$text;
        $msg  = sprintf("%s  %s \n", (new DateTimeImmutable())->format('Y-m-d H:i:s.v'), $text);

        $c = ApplicationContext::getContainer();
        $c->get(StdoutLoggerInterface::class)->{$method}($msg);
        $c->get(LoggerFactory::class)->get($channel)->{$method}($text);
    }
}
