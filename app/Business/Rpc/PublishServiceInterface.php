<?php

/******************************************************************************
 * Copyright (c) 2023.  M3-1-1 A.C.                                           *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Business\Rpc;

interface PublishServiceInterface
{
    public function getHtml(string $domain): string;

    public function saveGuestClick(array $data): bool;

    public function getImage(string $name): string;
}