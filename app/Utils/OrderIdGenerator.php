<?php
/******************************************************************************
 * Copyright (c) M3-1-1 AChang 2023.                                          *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Utils;

use Hyperf\Snowflake\IdGenerator\SnowflakeIdGenerator;

final readonly class OrderIdGenerator
{
    public function __construct(private SnowflakeIdGenerator $idGenerator) { }

    public function getId(string $prefix = ''): string
    {
        return $prefix.$this->idGenerator->generate();
    }
}