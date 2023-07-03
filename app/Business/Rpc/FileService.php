<?php

declare(strict_types=1);

namespace App\Business\Rpc;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\RpcServer\Annotation\RpcService;

#[RpcService(name: 'FileService', server: 'jsonrpc-http', protocol: 'jsonrpc-http')]
final class FileService implements FileServiceInterface
{
    #[Inject]
    private ContainerInterface $container;

    public function shouldSyncFile(string $name): bool
    {
        $publishService = $this->container->get(PublishServiceInterface::class);
        $image          = $publishService->getImage($name);
        return false;
    }
}
