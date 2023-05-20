<?php

namespace App\Services\Telegram\Group\Type;

use App\Services\Telegram\Group\Type\Entities\BotCommand;
use App\Services\Telegram\Group\Type\Entities\HashTag;

class Entities
{

    private HashTag $hasTag;
    private BotCommand $botCommand;
    private Url $url;


    public function __construct(HashTag $hashTag, BotCommand $botCommand, Url $url)
    {
        $this->hasTag = $hashTag;
        $this->botCommand = $botCommand;
        $this->url = $url;

    }

    public function filter(array $data)
    {

        if ($data["entities"][0]["type"] == "hashtag") {
            $this->hasTag->filter($data);
        }

        if ($data["entities"][0]["type"] == "bot_command") {
            $this->botCommand->filter($data);
        }

        foreach ($data["entities"] as $entities) {
            if ($entities["type"] == "url") {
                $this->url->filter($data);
                break;
            }
        }
    }

}
