<?php

/******************************************************************************
 * Copyright (c) 2023.  M3-1-1 A.C.                                           *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Business\Rpc;

interface PublishServiceInterface
{
    public function genHtmlByDomain(string $domain): string;

    public function getImage(string $path): string;

    public function getOssBaseUri(): string;

    public function getUnRegisteredDomainContent(): string;

    public static function getDomainKey(string $domain): string;

    public static function getCounterKey(): string;

    public static function flushMessaged(int $siteId): bool;
}
