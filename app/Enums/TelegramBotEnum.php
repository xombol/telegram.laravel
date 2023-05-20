<?php

namespace App\Enums;

enum TelegramBotEnum: string
{
    case CHAT_BOT = 'chat';
    case GROUP_BOT = 'supergroup';
}
