<?php

/******************************************************************************
 * Copyright (c) 2023.  M3-1-1 A.C.                                           *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Business\Rpc;

interface PublishServiceInterface
{
    public function getHtml(string $domain): string;

    public function saveClickInfo(array $data): bool;

    public function getImage(string $path): string;

    public function getOssBaseUri(): string;

    public function getUnRegisteredDomainContent(): string;
}