<?php

declare(strict_types=1);

namespace App\Business\Services;

use App\Exception\BusinessException;
use App\Utils\File\FileManager;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\Inject;

final class FileService
{
    #[Inject]
    private ContainerInterface $container;

    public function downFileFromOss(string $path): bool
    {
        $fm  = $this->container->get(FileManager::class);
        $oss = $fm->oss();
        if (!$fileBin = $oss->getFile($path)) {
            throw new BusinessException(message: '远程文件不存在 ' . $path);
        }
        $fs   = $fm->localFileSys();
        $path = PUBLIC_PATH . $path;
        $dir  = pathinfo($path, PATHINFO_DIRNAME);
        if (!$fs->isDirectory($dir)) {
            $fs->makeDirectory(path: $dir, recursive: true, force: true);
        }
        return $this->writeToPublic($fileBin, $path);
    }

    public function writeToPublic(string $image, string $path): bool
    {
        return (bool)file_put_contents($path, $image);
    }
}
