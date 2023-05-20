<?php

namespace App\Enums;

enum TelegramBotMethodEnum: string
{
    case SEND_MESSAGE = 'sendMessage';
    case DELETE_MESSAGE = 'deleteMessage';
    case SEND_PHOTO = 'sendPhoto';
    case GET_USER = 'getChatMember';
}
