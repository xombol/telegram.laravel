<?php

namespace App\Services\Telegram\Group;

use App\Services\Telegram\Group\Type\Entities;
use App\Services\Telegram\Group\Type\Entities\HashTag;
use App\Services\Telegram\Group\Type\Participant;
use App\Services\Telegram\Group\Type\Reply;
use App\Services\Telegram\Group\Type\Text;



class AnalyzeData
{

    private Participant $participant;
    private Entities $entities;
    private Text $text;
    private Reply $reply;

    public function __construct(Participant $participant, Entities $entities, Text $text, Reply $reply)
    {
        $this->participant = $participant;
        $this->entities = $entities;
        $this->text = $text;
        $this->reply = $reply;
    }

    public function processing(array $data)
    {
        /**
         * new user add to group
         */
        if (array_key_exists("new_chat_participant", $data)) {
            $this->participant->filter('new', $data);
        }
        /**
         * left user in group
         */
        if (array_key_exists("left_chat_participant", $data)) {
            $this->participant->filter('left', $data);
        }

        /**
         * all entities (хештеги)
         */
        if (array_key_exists("entities", $data)) {
            $this->entities->filter($data);
        }

        /**
         * analyze all text in data
         */
        if (array_key_exists("text", $data)) {
            $this->text->filter($data);
        }

        /**
         * analyze all reply in data
         */
        if (array_key_exists("reply_to_message", $data)) {
            $this->reply->filter($data);
        }

    }

}
