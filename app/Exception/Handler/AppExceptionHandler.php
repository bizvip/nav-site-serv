<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response): \Psr\Http\Message\MessageInterface|ResponseInterface
    {
        $this->logger->error(
            sprintf(
                '%s[%s] in %s',
                $throwable->getMessage(),
                $throwable->getLine(),
                $throwable->getFile()
            )
        );
        $this->logger->error($throwable->getTraceAsString());
        return $response->withHeader('Server', 'NSS1')->withStatus(500)->withBody(
            new SwooleStream('Internal Server Error.')
        );
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
