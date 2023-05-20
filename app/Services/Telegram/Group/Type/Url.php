<?php

namespace App\Services\Telegram\Group\Type;

use App\Enums\TelegramBotMethodEnum;
use App\Models\Telegram\Chat;
use App\Models\Telegram\Rating;
use App\Services\Telegram\Api;

class Url
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
        $this->user_permission_check($data);
    }

    /**
     * проверка рейтинга пользователя
     *
     * @return void
     */
    private function user_permission_check(array $data)
    {
        $chat = Chat::query()->where('chat_id', $data["chat"]["id"])->firstOrCreate();

        $user = Rating::query()->where('chat_id', $chat->id)->where('id_user_group', $data["from"]["id"])->first();

        if ($user == null) {
            $user = new Rating();
            $user->chat_id = $chat->id;
            $user->id_user_group = $data["from"]["id"];
            $user->value = 0;
            $user->save();
        }

        if ($user->all_permissions == true) {
            return;
        }

        if ($user->value < 40) {
            $params = [
                'chat_id' => $data["chat"]["id"],
                'parse_mode' => 'HTML',
                'text' => "<b>‼️Предупреждение!\n👀{$data["from"]["first_name"]} рейтинг ({$user->value}/40)</b><pre>Доступ к отправке ссылок ограничен!</pre>"
            ];

            $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, $params, $data["message_id"]);
        }
    }

}
