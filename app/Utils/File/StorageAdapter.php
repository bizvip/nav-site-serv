<?php

declare(strict_types=1);

namespace App\Utils\File;

use App\Exception\BusinessException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

final class StorageAdapter
{
    /**
     * [0] => __construct
     * [1] => getAdapter
     * [2] => has
     * [3] => write
     * [4] => writeStream
     * [5] => put
     * [6] => putStream
     * [7] => readAndDelete
     * [8] => update
     * [9] => updateStream
     * [10] => read
     * [11] => readStream
     * [12] => rename
     * [13] => copy
     * [14] => delete
     * [15] => deleteDir
     * [16] => createDir
     * [17] => listContents
     * [18] => getMimetype
     * [19] => getTimestamp
     * [20] => getVisibility
     * [21] => getSize
     * [22] => setVisibility
     * [23] => getMetadata
     * [24] => get
     * [25] => assertPresent
     * [26] => assertAbsent
     * [27] => addPlugin
     * [28] => __call
     * [29] => getConfig
     */
    private Filesystem $filesystem;

    public function setFilesystem(Filesystem $filesystem): StorageAdapter
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    public function deleteAll(string $dir = ''): bool|int
    {
        if ('' !== $dir) {
            return $this->filesystem->deleteDir($dir);
        }
        $list = $this->filesystem->listContents($dir, recursive: true);
        $i    = 0;
        foreach ($list as $k => $v) {
            if (($v['type'] === 'file') && $this->filesystem->delete($v['path'])) {
                $i++;
                continue;
            }
            if (($v['type'] === OSS::TYPE_DIR) && $this->filesystem->delete($v['path'])) {
                $i++;
            }
        }
        return $i;
    }

    public function getList(string $dir = '', bool $recursive = false): ?array
    {
        return $this->filesystem->listContents($dir, $recursive);
    }

    public function rename(string $path, string $newPath): bool
    {
        return $this->filesystem->rename($path, $newPath);
    }

    public function delete(string $path): bool
    {
        return $this->filesystem->delete($path);
    }

    public function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }

    public function copy(string $path, string $newPath): bool
    {
        try {
            return $this->filesystem->copy($path, $newPath);
        } catch (FileNotFoundException $fileNotFoundException) {
            return false;
        }
    }

    /**
     * Array
     * (
     * [path] => 11mp4.mp4
     * [dirname] =>
     * [basename] => 11mp4.mp4
     * [extension] => mp4
     * [filename] => 11mp4
     * [timestamp] => 1687861969
     * [size] => 13804174
     * [mimetype] => video/mp4
     * [metadata] => Array
     * (
     * )
     * [storageclass] =>
     * [etag] => "523fa9df13cb72823eb2ab2273315eaf"
     * [versionid] =>
     * [type] => file
     * )
     */
    public function getMeta(string $path): false|array
    {
        return $this->filesystem->getMetadata($path);
    }

    public function update(string $path, string $contents, array $config = []): bool
    {
        return $this->filesystem->update($path, $contents, $config);
    }

    public function createDir(string $dir, array $config = []): bool
    {
        return $this->filesystem->createDir($dir, $config);
    }

    public function deleteDir(string $dir): bool
    {
        return $this->filesystem->deleteDir($dir);
    }

    public function put(mixed $contents, string $path = '', array $config = [], bool $isAsync = false): bool
    {
        if (is_resource($contents)) {
            return $this->filesystem->putStream($path, $contents, $config);
        }
        return !is_string($contents)
            ? throw new BusinessException(message: '不支持的 put 内容类型')
            : $this->filesystem->put(path: $path, contents: file_get_contents($contents), config: $config);
    }

    public function write(mixed $contents, string $path = '', array $config = [], bool $isAsync = false): bool
    {
        if (is_resource($contents)) {
            return $this->filesystem->writeStream($path, $contents, $config);
        }
        return !is_string($contents)
            ? throw new BusinessException(message: '不支持的 write 内容类型')
            : $this->filesystem->write(path: $path, contents: file_get_contents($contents), config: $config);
    }

    public function getFile(string $path): string
    {
        return $this->filesystem->read($path);
    }

    public function getVisibility(string $path): mixed
    {
        return $this->filesystem->getVisibility($path);
    }

    public function setVisibilityPublic(string $path): bool
    {
        return $this->filesystem->setVisibility(path: $path, visibility: OSS::VISIBILITY_PUBLIC);
    }

    public function setVisibilityPrivate(string $path): bool
    {
        return $this->filesystem->setVisibility(path: $path, visibility: OSS::VISIBILITY_PRIVATE);
    }
}
