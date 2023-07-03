<?php

declare(strict_types=1);

namespace App\Utils\Enums;

enum AlphabetTypeEnums: string
{
    case LOWER = 'lower';
    case UPPER = 'upper';
    case MIXED = 'mixed';
}
