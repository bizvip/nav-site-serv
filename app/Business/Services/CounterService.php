<?php

declare(strict_types=1);

namespace App\Business\Services;

use App\Business\Biz;
use App\Business\Rpc\PublishServiceInterface;
use App\Utils\Str;
use DeviceDetector\DeviceDetector;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

final class CounterService
{
    #[Inject]
    private RequestInterface $request;

    #[Inject]
    private PublishServiceInterface $publishService;

    public function saveClick(): void
    {
        // todo 虽然受总数量限制，极高并发下数万协程会爆链接池
        // Coroutine::create(function () use ($headers, $params) {
        $params = $this->request->all();

        $id = !empty($params['url']) ? Str::hashToId(hash: $params['url'], salt: Biz::ID_HASH_SALT) : '';

        $domain = !empty($params['domain']) ? $params['domain'] : $this->request->url();

        $schema = !empty($params['schema'])
            ? rtrim($params['schema'], ':')
            : $this->request->getUri()->getScheme();

        $ua = !empty($params['asd']) ? $params['asd'] : $this->request->getHeaderLine('User-Agent');

        $dd = new DeviceDetector($ua);
        $dd->parse();

        //Array
        // (
        //     [type] => browser
        //     [name] => Chrome Mobile
        //     [short_name] => CM
        //     [version] => 80.0
        //     [engine] => Blink
        //     [engine_version] => 80.0.3987.162
        //     [family] => Chrome
        // )
        //Array
        // (
        //     [name] => Android
        //     [short_name] => AND
        //     [version] => 10
        //     [platform] =>
        //     [family] => Android
        // )

        $this->publishService->saveClickInfo([
            'id'         => $id,
            'domain'     => $domain,
            'schema'     => $schema,
            'ua'         => $ua,
            'type'       => $dd->getClient('type'),
            'name'       => $dd->getClient('name'),
            'version'    => $dd->getClient('version'),
            'os'         => $dd->getOs('name'),
            'os_version' => $dd->getOs('version'),
        ]);

        $dd = null;
    }
}
