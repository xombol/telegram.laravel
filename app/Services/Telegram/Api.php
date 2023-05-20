<?php

namespace App\Services\Telegram;

use App\Enums\TelegramBotMethodEnum;
use App\Models\Telegram\Chat;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class Api extends Telegram
{


    /**
     * send/destroy  message
     *
     * @param TelegramBotMethodEnum $type
     * @param array $params
     * @param $message_id
     * @return \Illuminate\Http\Client\Response
     */
    public function send(TelegramBotMethodEnum $type, array $params, $message_id = null)
    {
        if ($message_id) {
            $this->remove_get_message($params["chat_id"], $message_id);
        }

        return Http::post($this->getUrl() . $this->getToken($params["chat_id"]) . '/' . $type->value, $params);

    }

    /**
     * remove user message / request
     *
     * @param $chat_id
     * @param $message_id
     * @return void
     */
    private function remove_get_message($chat_id, $message_id)
    {
        Http::post($this->getUrl() . $this->getToken($chat_id) . '/' . TelegramBotMethodEnum::DELETE_MESSAGE->value, ['chat_id' => $chat_id, "message_id" => $message_id]);
    }

    /**
     * get token and decrypt
     *
     * @param $chatId
     * @return string
     */
    public function getUser(string $chat_id, string $message_id): string
    {
        return Http::post($this->getUrl() . $this->getToken($chat_id) . '/' . TelegramBotMethodEnum::GET_USER->value, ['chat_id' => $chat_id, "user_id" => $message_id])->body();
    }


    /**
     * get token and decrypt
     *
     * @param $chatId
     * @return string
     */
    private function getToken($chatId)
    {
        $chat = Chat::query()->where('chat_id', '=', $chatId)->first();
        return Crypt::decryptString($chat->telegram->token);
    }
}
