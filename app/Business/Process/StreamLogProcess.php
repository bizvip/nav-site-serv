<?php

declare(strict_types=1);

namespace App\Business\Process;

use App\Business\Rpc\Publish;
use App\Business\Rpc\PublishServiceInterface;
use App\Utils\Logger;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Redis\Redis;

#[Process(name: 'stream-log-save')]
final class StreamLogProcess extends AbstractProcess
{
    public int $nums = 1;

    #[Inject]
    private PublishServiceInterface $publishService;

    #[Inject]
    private Redis $redis;

    public function handle(): void
    {
        while (true) {
            try {
                $this->process();
            } catch (\Throwable $e) {
                Logger::error($e);
            }
            sleep(60);
        }
    }

    private function process(): void
    {
        $items = $this->redis->xRead([Publish::COUNTER_KEY => '0-0'], 500, -1);
        Logger::info(count($items[Publish::COUNTER_KEY]));
        if (count($items[Publish::COUNTER_KEY]) > 0) {
            $list = $this->publishService->batchWriteToDb($items[Publish::COUNTER_KEY]);
            foreach ($list as $v) {
                $this->redis->xDel(Publish::COUNTER_KEY, $v);
            }
        }
    }
}
