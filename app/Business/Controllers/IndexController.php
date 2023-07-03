<?php

declare(strict_types=1);

namespace App\Business\Controllers;

use App\Business\Rpc\PublishServiceInterface;
use App\Business\Services\ClickCountService;
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
    private PublishServiceInterface $publishService;

    #[Inject]
    private ClickCountService $clickCountService;

    #[GetMapping(path: '')]
    public function index(): ResponseInterface
    {
        try {
            $headers = $this->request->getHeaders();
            $host    = parse_url($headers['host'][0]);

            if (isset($host['path'])) {
                $host = !empty($host) ? str_ireplace('www.', '', $host['path']) : null;
            } elseif (isset($host['host'])) {
                $host = !empty($host) ? str_ireplace('www.', '', $host['host']) : null;
            } else {
                Logger::error(
                    ['get host header failed, set to default ""', $headers, $host]
                );
                $host = '';
            }
            $contents = $this->publishService->getHtml($host);
        } catch (\Throwable $e) {
            Logger::error($e);
            $contents = '';
        }

        return $this->response
            ->withHeader('Content-Type', 'text/html')
            ->withHeader('Cache-Control', 'public, max-age=120')
            ->withBody(new SwooleStream($contents));
    }

    #[PostMapping(path: 'count')]
    public function clickCount(): ResponseInterface
    {
        print_r($this->request->all());
        print_r($this->request->getHeaders());
        return $this->response->withStatus(204);
    }
}
