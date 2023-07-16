<?php

declare(strict_types=1);

namespace App\Business\Services;

use App\Business\Rpc\PublishServiceInterface;
use App\Exception\BusinessException;
use App\Utils\File\FileManager;
use App\Utils\Logger;
use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;

final class SyncService
{
    #[Value('file.storage.local.public_root')]
    private string $localPublicRoot;

    #[Value('file.storage.local.frontend_root')]
    private string $frontendRoot;

    #[Inject]
    private FileManager $fileManager;

    #[Inject]
    private PublishServiceInterface $publishService;

    /**
     * Array
     * (
     * [func] => sync
     * [publishName] => Mwj9b1evKMWV.js
     * )
     */
    public function sync(array $data): bool
    {
        if (!isset($data['publishName'])) {
            print_r($data);
            throw new BusinessException(message: '收到的消息不合法');
        }

        $content = $this->publishService->getImage($data['publishName']);
        echo strlen($content),PHP_EOL;
        $image   = base64_decode($content);

        $localDir      = PUBLIC_PATH . $data['path']['dirname'];
        $localFileName = PUBLIC_PATH . $data['path']['dirname'] . DIRECTORY_SEPARATOR . $data['publishName'];

        $fs = $this->fileManager->localFileSys();

        echo $localDir,PHP_EOL,$localFileName,PHP_EOL;
        if ($fs->isDirectory($localDir)) {
            return file_put_contents($localFileName, $image) > 0;
        }

        $fs->makeDirectory($localDir, 0755, true, true);
        return file_put_contents($localFileName, $image) > 0;
    }

    public function getFileFromOss(string $path): string
    {
        $oss = $this->fileManager->oss();
        if (!$fileBin = $oss->getFile($path)) {
            throw new BusinessException(message: '远程文件不存在 ' . $path);
        }
        $fs   = $this->fileManager->localFileSys();
        $path = PUBLIC_PATH . $path;
        $dir  = pathinfo($path, PATHINFO_DIRNAME);
        if (!$fs->isDirectory($dir)) {
            $fs->makeDirectory(path: $dir, recursive: true, force: true);
        }
        return $fileBin;
    }

    public function saveToLocalPublic(string $fileContents, string $filePath): bool
    {
        $fs = $this->fileManager->localFileSys();
        if ($fs->isDirectory($this->localPublicRoot)) {
            $fs->makeDirectory(path: $this->localPublicRoot, recursive: true, force: true);
        }
        if (!is_link($this->frontendRoot)) {
            try {
                if (is_file($this->frontendRoot)) {
                    $fs->delete($this->frontendRoot);
                }
                if (is_dir($this->frontendRoot)) {
                    $fs->deleteDirectories($this->frontendRoot);
                }
                $fs->link($this->localPublicRoot, $this->frontendRoot);
            } catch (\Throwable $e) {
                Logger::error($e);
            }
        }
        return (bool)file_put_contents($this->localPublicRoot . $filePath, $fileContents);
    }
}
