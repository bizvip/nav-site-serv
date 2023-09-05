<?php

declare(strict_types=1);

namespace App\Business\Controllers;

use App\Business\Services\IndexService;
use App\Controller\AbstractController;
use App\Utils\Logger;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: '/')]
final class IndexController extends AbstractController
{
    #[Inject]
    private IndexService $indexService;

    #[GetMapping(path: 'home')]
    public function index(): ResponseInterface
    {
        try {
            $headers = $this->request->getHeaders();
            $host    = parse_url($headers['host'][0]);

            if (isset($host['path'])) {
                $domain = !empty($host['path']) ? str_ireplace(search: 'www.', replace: '', subject: $host['path']) : null;
            } elseif (isset($host['host'])) {
                $domain = !empty($host['host']) ? str_ireplace(search: 'www.', replace: '', subject: $host['host']) : null;
            } else {
                $domain = 'cl0001.wzjmmr.top';
            }

            $contents = $this->indexService->getHtmlFromCache($domain);
        } catch (\Throwable $e) {
            Logger::error($e);
            $contents = $this->indexService->getUnRegisteredDomainContent();
        }

        return $this->response
            ->withHeader('Content-Type', 'text/html')
            ->withHeader('Cache-Control', 'public, max-age=60')
            ->withBody(new SwooleStream($contents));
    }

    #[PostMapping(path: 'boom')]
    public function counter(): ResponseInterface
    {
        $this->indexService->saveClick();
        return $this->response->withStatus(200);
    }
}
