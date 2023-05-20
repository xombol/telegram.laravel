<?php

namespace App\Services\Telegram\Group\Type;

use App\Enums\TelegramBotMethodEnum;
use App\Models\Telegram\Chat;
use App\Models\Telegram\Rating;
use App\Services\Telegram\Api;

class Participant
{
    private Api $api;

    /**
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * проверка (вышел/зашёл)
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    public function filter(string $type, array $data)
    {
        if ($type == 'new') {
            $this->new_participant($data);
        }
        if ($type == 'left') {
            $this->left_participant($data);
        }

    }

    /**
     * новый пользователь
     *
     * @param array $data
     * @return void
     */
    private function new_participant(array $data)
    {
        $name = (!array_key_exists('username', $data["new_chat_participant"]))
            ? $data["new_chat_participant"]["first_name"]
            : "{$data["new_chat_participant"]["first_name"]} (@{$data["new_chat_participant"]["username"]})";

        $params = [
            'chat_id' => $data["chat"]["id"],
            'parse_mode' => 'HTML', 'text' => "<pre>У нас новенький</pre><b>\r\n" . $name . "🙋</b>",
        ];
        $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, $params, $data["message_id"]);
    }

    /**
     * покинул группу
     *
     * @param array $data
     * @return void
     */
    private function left_participant(array $data)
    {
        $name = (!array_key_exists('username', $data["left_chat_participant"]))
            ? $data["left_chat_participant"]["first_name"]
            : "{$data["left_chat_participant"]["first_name"]} ({$data["left_chat_participant"]["username"]})";


        $params = [
            'chat_id' => $data["chat"]["id"],
            'parse_mode' => 'HTML', 'text' => "<pre>Покинул</pre><b>\r\n" . $name . "🍷</b>",
        ];
        $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, $params, $data["message_id"]);
    }

}
