<?php

namespace App\Services\Telegram\Group\Type;

use App\Enums\TelegramBotMethodEnum;
use App\Services\Telegram\Api;

class Text
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
        if ($this->search('скучно', $data)) {
            $this->txt_weather($data);
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
        return (($data["text"] == $text) || (str_contains($data["text"], $text)));
    }

    /**
     * функция на слово "скучно"
     *
     * @return void
     */
    private function txt_weather(array $data)
    {
        $this->api->send(TelegramBotMethodEnum::SEND_PHOTO, [
            'chat_id' => $data["chat"]["id"],
            'photo' => 'https://165dc6ae-1b27-4856-8ca7-b1edf208847c.selcdn.net/images/original/materials/frontPhotos/18062/3316.jpg?1669787271',
            'caption' => $data["from"]["first_name"] . " «Нечем заняться - взял(а) начистил(а) картошки и приготовила, соседей угости, там не знаю, кому-нибудь еще что-то сделай, помой полы, подъезд подмети..»",
        ], $data["message_id"]);
    }

}
