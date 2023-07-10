<?php

declare(strict_types=1);

namespace App\Business\Rpc;

final readonly class Publish
{
    final public const STREAM_COUNTER_KEY = 'sm:counter';

    public static function domainKey(string $domain): string { return 'c:html:' . $domain; }
}
