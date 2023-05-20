<?php

namespace App\Services\Telegram\Group\Type;

use App\Enums\TelegramBotMethodEnum;
use App\Models\Telegram\Chat;
use App\Models\Telegram\Rating;
use App\Services\Telegram\Api;

class Reply
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Проверка на совпадение
     *
     * @return void
     */
    public function filter(array $data)
    {
        if ($this->search('+', $data)) {
            $this->txt_plus($data, 'plus');
        }
        if ($this->search('-', $data)) {
            $this->txt_plus($data, 'minus');
        }

    }

    /**
     * поиск в строке или проверка срау строки
     * (доработать для проверки на массив)
     *
     * @param string $text
     * @return bool
     */
    private function search(string $text, array $data): bool
    {
        return ($data["text"] == $text);
    }

    /**
     * функция на слово "скучно"
     *
     * @return void
     */
    private function txt_plus(array $data, string $operation)
    {

        $user = (array_key_exists('username', $data["from"])) ? '@' . $data["from"]["username"] : $data["from"]["first_name"];
        $user_to_reply = (array_key_exists('username', $data["reply_to_message"]["from"])) ? '@' . $data["reply_to_message"]["from"]["username"] : $data["reply_to_message"]["from"]["first_name"];

        if ($user == $user_to_reply) {
            $this->api->send(TelegramBotMethodEnum::DELETE_MESSAGE, ['chat_id' => $data["chat"]["id"], "message_id" => $data["message_id"]]);
            return;
        }

        if ($data["reply_to_message"]["from"]["is_bot"] == true) {
            $this->api->send(TelegramBotMethodEnum::DELETE_MESSAGE, ['chat_id' => $data["chat"]["id"], "message_id" => $data["message_id"]]);
            return;
        }

        $chat = Chat::query()->where('chat_id', $data["chat"]["id"])->first();

        $user_data = Rating::query()
            ->where('id_user_group', $data["reply_to_message"]["from"]["id"])
            ->where('chat_id', $chat->id)
            ->first();

        if (!$user_data) {
            $rating = new Rating();
            $rating->chat_id = $chat->id;
            $rating->id_user_group = $data["reply_to_message"]["from"]["id"];
            $rating->value = 1;
            $rating->save();

            $user_st = 1;
        } else {
            $user_data->value = $operation == 'plus' ? $user_data->value + 1 : $user_data->value - 1;
            $user_data->update();
            $user_st = $user_data->value;
        }


        $message = $operation == 'plus'
            ? '✈ ' . $user . ' повысил уровень ' . $user_to_reply . '<b> (' . $user_st . ')</b> '
            : '⚓ ' . $user . ' понизил уровень ' . $user_to_reply . '<b> (' . $user_st . ')</b> ';

        $params = [
            'chat_id' => $data["chat"]["id"],
            'parse_mode' => 'HTML', 'text' => $message,
        ];
        $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, $params);
    }


}
