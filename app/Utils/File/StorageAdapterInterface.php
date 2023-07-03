<?php

declare(strict_types=1);

namespace App\Utils\File;

use League\Flysystem\Filesystem;

interface StorageAdapterInterface
{
    public function setFilesystem(Filesystem $filesystem): StorageAdapterInterface;

    public function getList(string $dir = '', bool $recursive = false): ?array;

    public function rename(string $path, string $newPath): bool;

    public function delete(string $path): bool;

    public function has(string $path): bool;

    public function copy(string $path, string $newPath): bool;

    public function getMeta(string $path): false|array;

    public function update(string $path, string $contents, array $config = []): bool;

    public function createDir(string $dir, array $config = []): bool;

    public function deleteDir(string $dir): bool;

    public function put(mixed $contents, string $path, array $config = [], bool $isAsync = false): bool;

    public function write(mixed $contents, string $path, array $config = [], bool $isAsync = false): bool;

    public function getFile(string $path): string;

    public function getVisibility(string $path): mixed;

    public function setVisibilityPublic(string $path): bool;

    public function setVisibilityPrivate(string $path): bool;
}
