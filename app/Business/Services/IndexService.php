<?php

declare(strict_types=1);

namespace App\Business\Services;

use App\Business\Biz;
use App\Business\Rpc\Publish;
use App\Business\Rpc\PublishServiceInterface;
use App\Exception\BusinessException;
use App\Utils\Logger;
use App\Utils\Packer;
use App\Utils\Str;
use DeviceDetector\DeviceDetector;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;

final class IndexService
{
    #[Inject]
    private RequestInterface $request;

    #[Inject]
    private PublishServiceInterface $publishService;

    #[Inject]
    private Redis $redis;

    public final const HTML_TTL = 0;

    public function flush(array $data): bool
    {
        if (empty($data['domains'])) {
            throw new BusinessException(message: '没有任何域名，消费者无刷新');
        }

        foreach ($data['domains'] as $domain) {
            try {
                $html = $this->publishService->getHtmlBuildFromDomain($domain);
                if (mb_strlen($html) > 10) {
                    $this->redis->set(Publish::getDomainKey($domain), Packer::serialize($html));
                }
                sleep(1);
            } catch (\Throwable $e) {
                Logger::error($e);
                continue;
            }
        }

        return true;
    }

    public function saveClick(): void
    {
        // 虽然受总数量限制，极高并发下上万协程会爆链接池
        // Coroutine::create(function () use ($headers, $params) {
        $params = $this->request->all();

        $id = !empty($params['url']) ? Str::hashToId(hash: $params['url'], salt: Biz::ID_HASH_SALT)
            : '';

        $domain = !empty($params['domain']) ? $params['domain'] : $this->request->url();

        $schema = !empty($params['schema'])
            ? rtrim($params['schema'], ':')
            : $this->request->getUri()->getScheme();

        $ua = !empty($params['asd']) ? $params['asd'] : $this->request->getHeaderLine('User-Agent');

        $dd = new DeviceDetector($ua);
        $dd->parse();

        // Array
        // (
        //     [type] => browser
        //     [name] => Chrome Mobile
        //     [short_name] => CM
        //     [version] => 80.0
        //     [engine] => Blink
        //     [engine_version] => 80.0.3987.162
        //     [family] => Chrome
        // )

        // Array
        // (
        //     [name] => Android
        //     [short_name] => AND
        //     [version] => 10
        //     [platform] =>
        //     [family] => Android
        // )

        $this->pushClickToStream([
            'ip'         => $this->request->getServerParams()['remote_addr'],
            'click_time' => date('Y-m-d H:i:s'),
            'id'         => $id,
            'domain'     => $domain,
            'schema'     => $schema,
            'ua'         => $ua,
            'brand'      => $dd->getBrandName(),
            'type'       => $dd->getClient('type'),
            'name'       => $dd->getClient('name'),
            'version'    => $dd->getClient('version'),
            'os'         => $dd->getOs('name'),
            'os_version' => $dd->getOs('version'),
        ]);

        $dd = null;
    }

    public function pushClickToStream(array $data): bool
    {
        try {
            $data['id'] = $data['id'][0] ?? null;
            if (!$data['id']) {
                return false;
            }
            $xId = $this->redis->xAdd(Publish::COUNTER_KEY, '*', $data, 1000000, true);
            return is_string($xId) && strlen($xId) > 10;
        } catch (\Throwable $e) {
            Logger::error($e);
            return false;
        }
    }

    #[Cacheable(prefix: 'html', value: '#{domain}', ttl: self::HTML_TTL)]
    public function getHtmlFromCache(string $domain): string
    {
        return $this->publishService->getHtmlBuildFromDomain($domain);
    }

    #[Cacheable(prefix: 'unknown', ttl: self::HTML_TTL)]
    public function getUnRegisteredDomainContent(): string
    {
        return $this->publishService->getUnRegisteredDomainContent();
    }
}
