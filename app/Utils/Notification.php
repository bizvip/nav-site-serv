<?php

/******************************************************************************
 * Copyright (c) 2023.  M3-1-1 A.C.                                           *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Utils;

use App\System\Model\SystemQueueMessage;
use App\System\Service\SystemQueueLogService;
use Hyperf\Context\ApplicationContext;

final class Notification
{
    public static function sendSystemUserMessage(int $userId, string $title, string $content = '', array $receiveUsers = [], string $type = SystemQueueMessage::TYPE_PRIVATE_MESSAGE): bool
    {
        return ApplicationContext::getContainer()->get(SystemQueueLogService::class)
            ->pushMessage(
                message     : (new \App\System\Vo\QueueMessageVo())->setTitle($title . ' ' . date('Y-m-d H:i:s'))
                    ->setContentType($type)->setContent($content.' <p>dsafdsaf</p><h3>dasjfio21342</h3>')
                    ->setSendBy($userId),
                receiveUsers: $receiveUsers
            );
    }

    public static function sendTelegramMessage(string $text, string $chatId, string $parseMode = 'markdown'): bool
    {
        /** @var TgHelper $tg */
        $tg = ApplicationContext::getContainer()->get(TgHelper::class);
        $tg->setChatId($chatId);
        if ($parseMode !== 'markdown') {
            $tg->setParseMode($parseMode);
        }
        return $tg->sendMessage($text);
    }
}
