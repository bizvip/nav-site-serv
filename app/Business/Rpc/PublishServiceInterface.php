<?php

/******************************************************************************
 * Copyright (c) 2023.  M3-1-1 A.C.                                           *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Business\Rpc;

interface PublishServiceInterface
{
    public function getHtmlBuildFromDomain(string $domain): string;

    public function getImage(string $publishName): string;

    public function getOssBaseUri(): string;

    public function getUnRegisteredDomainContent(): string;

    public function batchWriteToDb(array $items): array;
}
