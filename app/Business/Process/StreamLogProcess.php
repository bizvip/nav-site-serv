<?php

declare(strict_types=1);

namespace App\Business\Process;

use App\Business\Rpc\Publish;
use App\Business\Rpc\PublishServiceInterface;
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
                echo $e->getMessage(), PHP_EOL;
            }
            sleep(60);
        }
    }

    private function process(): void
    {
        $items = $this->redis->xRead([Publish::COUNTER_KEY => '0-0'], 500, -1);
        var_dump(count($items));
        if (isset($items[Publish::COUNTER_KEY])) {
            $list = $this->publishService->batchWriteToDb($items[Publish::COUNTER_KEY]);
            var_dump($list);
            foreach ($list as $v) {
                $this->redis->xDel(Publish::COUNTER_KEY, $v);
            }
        }
    }
}
