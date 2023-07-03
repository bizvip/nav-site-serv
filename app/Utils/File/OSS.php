<?php

declare(strict_types=1);

namespace App\Utils\File;

final readonly class OSS
{
    final public const VISIBILITY_PUBLIC  = 'public';
    final public const VISIBILITY_PRIVATE = 'private';
    final public const TYPE_FILE          = 'file';
    final public const TYPE_DIR           = 'dir';

    final public const DIR_NAME_VIDEO = 'video';
    final public const DIR_NAME_IMAGE = 'image';
}