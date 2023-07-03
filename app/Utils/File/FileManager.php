<?php

declare(strict_types=1);

namespace App\Utils\File;

use Hyperf\Context\ApplicationContext;
use Hyperf\Filesystem\FilesystemFactory;

final class FileManager
{
    private FilesystemFactory $factory;

    public function __construct(FilesystemFactory $filesystemFactory) { $this->factory = $filesystemFactory; }

    public function oss(): StorageAdapter
    {
        return ApplicationContext::getContainer()->get(StorageAdapter::class)
            ->setFilesystem($this->factory->get('layer_s3'));
    }

    public function local(): StorageAdapter
    {
        return ApplicationContext::getContainer()->get(StorageAdapter::class)
            ->setFilesystem($this->factory->get('local'));
    }
}
