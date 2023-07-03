<?php

declare(strict_types=1);

namespace App\Business\Process;

use App\Utils\TgHelper;
use Hyperf\Config\Annotation\Value;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;

use function Hyperf\Support\make;

// #[Process(name: 'tg-updates-process')]
final class TgUpdatesProcess extends AbstractProcess
{
    public int $nums = 1;

    #[Value('tg.bot_token')]
    private string $token;

    #[Value('tg.bot_name')]
    private string $botName;

    public function handle(): void
    {
        while (true) {
            /** @var TgHelper $tg */
            $tg = make(TgHelper::class);
            $tg->getUpdates();
            unset($tg);
            sleep(5);
        }
    }
}
