<?php

namespace App\Services\Telegram;

use App\Enums\TelegramBotEnum;
use App\Enums\TelegramBotMethodEnum;
use App\Models\Telegram\Chat;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class ExceptionTelegram extends Telegram
{

    private const ADMIN_ID = '697721198';

    /**
     * send exception message to admin
     *
     * @param TelegramBotMethodEnum $type
     * @param array $params
     * @param $message_id
     * @return Response
     */
    public function send($exception, array $chat, string $message_id)
    {

        $message_ex = $exception->getMessage();
        $file_ex = $exception->getFile();
        $line_ex = $exception->getLine();

        $chat_data_to_string = '';
        foreach ($chat as $key => $item) {
            $chat_data_to_string .= "{$key} -> {$item} \n";
        }

        $params = [
            'chat_id' => self::ADMIN_ID,
            'parse_mode' => 'HTML', 'text' =>
                "<b>Ошибка:</b><pre>{$message_ex}</pre>" .
                "<b>Файл:</b><pre>{$file_ex}:{$line_ex}</pre>" .
                "\n\n" .
                "<b>Смс (ид):</b><pre>{$message_id}</pre>" .
                "<b>Данные чата:</b><pre>{$chat_data_to_string}</pre>",
        ];

        return Http::post($this->getUrl() . $this->getToken($chat["id"]) . '/' . TelegramBotMethodEnum::SEND_MESSAGE->value, $params);
    }

    /**
     * get token and decrypt
     *
     * @param $chatId
     * @return string
     */
    private function getToken($chatId): string
    {
        $chat = Chat::query()->where('chat_id', '=', $chatId)->first();
        return Crypt::decryptString($chat->telegram->token);
    }
}
