<?php

declare(strict_types=1);

namespace App\Utils;

use App\Exception\BusinessException;
use Hyperf\Config\Annotation\Value;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

final class TgHelper
{
    #[Value('tg.bot_token')]
    private string $botToken;

    #[Value('tg.bot_name')]
    private string $botName;

    private string $chatId = '';

    private string $parseMode = 'markdown';

    private ?Telegram $telegram = null;

    public function setParseMode(string $parseMode): TgHelper
    {
        $this->parseMode = $parseMode;
        return $this;
    }

    private function handleTelegram(): Telegram
    {
        if (!$this->telegram) {
            $this->telegram = new \Longman\TelegramBot\Telegram($this->botToken, $this->botName);
        }
        return $this->telegram;
    }

    public function setChatId(string $chatId): self
    {
        $this->chatId = $chatId;
        return $this;
    }

    public function chooseGroup(): self { return $this; }

    public function getUpdates()
    {
        try {
            /**
             * Check `hook.php` for configuration code to be added here.
             */
            $this->handleTelegram()->useGetUpdatesWithoutDatabase();

            // Handle telegram getUpdates request
            $serverResponse = $this->handleTelegram()->handleGetUpdates();

            if ($serverResponse->isOk()) {
                $newCount = count($serverResponse->getResult());
                print_r($serverResponse->getResult());
                $msg = date('Y-m-d H:i:s') . ' - Processed ' . $newCount . ' updates';
            } else {
                $msg = date('Y-m-d H:i:s') . ' - Failed to fetch updates' . PHP_EOL . $serverResponse->printError();
            }
            console()->info($msg);
        } catch (\Longman\TelegramBot\Exception\TelegramException $e) {
            echo $e;
            \Longman\TelegramBot\TelegramLog::error((string)$e);
        }
    }

    /**
     * @markdown
     * *bold \*text*
     * _italic \*text_
     * __underline__
     * ~strikethrough~
     * ||spoiler||
     * bold _italic bold ~italic bold strikethrough ||italic bold strikethrough spoiler||~ __underline italic bold___ bold*
     * [inline URL](http://www.example.com/)
     * [inline mention of a user](tg://user?id=123456789)
     * ![👍](tg://emoji?id=5368324170671202286)
     * `inline fixed-width code`
     * ```
     * pre-formatted fixed-width code block
     * ```
     * ```python
     * pre-formatted fixed-width code block written in the Python programming language
     * ```
     * @html
     *<b>bold</b>, <strong>bold</strong>
     * <i>italic</i>, <em>italic</em>
     * <u>underline</u>, <ins>underline</ins>
     * <s>strikethrough</s>, <strike>strikethrough</strike>, <del>strikethrough</del>
     * <span class="tg-spoiler">spoiler</span>, <tg-spoiler>spoiler</tg-spoiler>
     * <b>bold <i>italic bold <s>italic bold strikethrough <span class="tg-spoiler">italic bold strikethrough spoiler</span></s> <u>underline italic bold</u></i> bold</b>
     * <a href="http://www.example.com/">inline URL</a>
     * <a href="tg://user?id=123456789">inline mention of a user</a>
     * <tg-emoji emoji-id="5368324170671202286">👍</tg-emoji>
     * <code>inline fixed-width code</code>
     * <pre>pre-formatted fixed-width code block</pre>
     * <pre><code class="language-python">pre-formatted fixed-width code block written in the Python programming language</code></pre>
     */
    public function sendMessage(string $text): bool
    {
        Request::initialize($this->handleTelegram());
        $resp = Request::sendMessage([
            'chat_id'    => $this->chatId,
            'parse_mode' => $this->parseMode,
            'text'       => $text,
        ]);
        if (!$resp->isOk()) {
            throw new BusinessException(message: sprintf('tg 消息发送给 %s 失败了 : %s', $this->chatId, $resp->printError()));
        }
        return true;
    }
}
