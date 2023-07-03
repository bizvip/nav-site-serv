<?php

declare(strict_types=1);

namespace App\Business\Rpc;

interface FileServiceInterface
{
    public function shouldSyncFile(string $name): bool;
}