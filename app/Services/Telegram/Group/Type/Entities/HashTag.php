<?php

namespace App\Services\Telegram\Group\Type\Entities;

use App\Enums\TelegramBotMethodEnum;
use App\Models\Telegram\Chat;
use App\Models\Telegram\Rating;
use App\Services\Quote\Quote;
use App\Services\Telegram\Api;
use App\Services\Weather\Weather;


class HashTag
{
    private Weather $weather;
    private Quote $quote;
    private Api $api;

    /**
     * @property Weather $weather
     */
    public function __construct(Weather $weather, Quote $quote, Api $api)
    {
        $this->weather = $weather;
        $this->quote = $quote;
        $this->api = $api;
    }

    /**
     * Проверка на совпадение
     *
     * @return void
     */
    public function filter(array $data)
    {
        if ($this->search('#погода', $data)) {
            $this->ent_weather($data);
        }
        if ($this->search('#цитата', $data)) {
            $this->ent_quote($data);
        }
        if ($this->search('#топ', $data)) {
            $this->ent_top($data);
        }

    }

    /**
     * поиск в строке или проверка срау строки
     *
     * @param string $text
     * @return bool
     */
    private function search(string $text, array $data): bool
    {
        return (($data["text"] == $text) || (str_contains($data["text"], $text)));
    }

    /**
     * #погода и все различные его варианты
     *
     * @param Weather $weather
     * @return void
     */
    private function ent_weather(array $data)
    {

        $city = explode(' ', $data["text"], 2);
        if (!array_key_exists(1, $city)) {
            $city[1] = "Кишинёв";
        }

        $data_weather = $this->weather->search($city[1]);

        if ($data_weather["count"] == 0) {
            $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, ['chat_id' => $data["chat"]["id"], 'parse_mode' => 'HTML', 'text' => "<pre>Скорее всего город был указан не верно или название не найдено!!🧐</pre>"]);
            return;
        }
        $message_weather = '';
        foreach ($data_weather["list"] as $weather) {
            $message_weather .= $weather["name"] . ' (' . $weather["sys"]["country"] . ") \n";
            $message_weather .= "Ветер : {$weather["wind"]["speed"]} м/с \n";

            $weather["main"]["temp"] ? $message_weather .= "Температура : " . round($weather["main"]["temp"] - 271) : null;
            $weather["main"]["feels_like"] ? $message_weather .= "°C\nОщущается как : " . round($weather["main"]["feels_like"] - 271) : null;
            $weather["main"]["pressure"] ? $message_weather .= "°C\nДавление : {$weather["main"]["pressure"]} мм рт. ст  \n" : null;
            $weather["main"]["humidity"] ? $message_weather .= "Влажность : {$weather["main"]["humidity"]}% \n" : null;

            $weather["rain"] ? $message_weather .= "На улице дождь \n" : null;
            $weather["snow"] ? $message_weather .= "На улице идёт снег  \n" : null;
            $weather["clouds"]["all"] ? $message_weather .= "Облака : {$weather["clouds"]["all"]}% \n" : null;

            $message_weather .= "\n";
        }

        $params = [
            'chat_id' => $data["chat"]["id"],
            'parse_mode' => 'HTML',
            'text' => "<b>Погода по запросу  </b><pre>{$data["text"]} \n\n" . $message_weather . "</pre><b>На всякий случай выгляни в окно !🤗</b>"
        ];

        $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, $params, true);

    }

    /**
     * #цитата рандомная
     *
     * @return void
     */
    private function ent_quote(array $data)
    {
        $res = $this->quote->random();
        $params = ['chat_id' => $data["chat"]["id"], 'parse_mode' => 'HTML', 'text' => "<b>{$data["text"]}</b><pre>" . $res . "</pre>"];

        $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, $params);
    }

    private function ent_top(array $data)
    {
        $chat = Chat::query()->where('chat_id', $data["chat"]["id"])->first();

        $top_us = Rating::query()->orderBy('value', 'desc')->where('chat_id', $chat->id)->limit(7)->get();

        $top_list = "Топ 7 чата:\n";

        foreach ($top_us as $key => $item) {
            $data_us = json_decode($this->api->getUser($data["chat"]["id"], $item->id_user_group), true);

            $first_name = array_key_exists("last_name", $data_us["result"]["user"]) ? ' ' . $data_us["result"]["user"]["last_name"] : '';
            $user_name = array_key_exists("username", $data_us["result"]["user"]) ? ' @' . $data_us["result"]["user"]["username"] : '';

            $top_list .= $key + 1 . '. ' . $data_us["result"]["user"]["first_name"] . $first_name . $user_name . ' (' . $item->value . ")\n";

        }

        $params = ['chat_id' => $data["chat"]["id"], 'parse_mode' => 'HTML', 'text' => "<b>{$top_list}</b>"];

        $this->api->send(TelegramBotMethodEnum::SEND_MESSAGE, $params);

    }

}
