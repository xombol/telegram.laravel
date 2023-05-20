<?php

namespace App\Services\Telegram;

use App\Enums\TelegramBotEnum;
use App\Services\Telegram\type\TelegramGroup;
use App\Services\Telegram\Group\Group;
use Illuminate\Support\Facades\Http;

class Telegram
{


    public const URL_API = 'https://api.telegram.org/bot';
    public const GET_ME = '/getMe';

    protected function getUrl(): string
    {
        return self::URL_API;
    }

    /**
     * get conection
     * @return boolean
     * example url https://api.telegram.org/bot{token}/getMe
     */
    public function connection(string $token): bool
    {
        $connection = Http::post(self::URL_API . $token . self::GET_ME);
        return (bool)$connection["ok"];
    }

}
