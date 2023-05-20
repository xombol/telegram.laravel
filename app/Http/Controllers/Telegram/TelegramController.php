<?php

namespace App\Http\Controllers\Telegram;

use App\Enums\TelegramBotEnum;
use App\Http\Controllers\Controller;
use App\Models\MessageTelegram;
use App\Models\Telegram\Chat;
use App\Services\Telegram\ExceptionTelegram;
use App\Services\Telegram\Group\Group;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class TelegramController extends Controller
{

    public function token(Request $request, Group $group, ExceptionTelegram $exceptionTelegram)
    {

        $data = $request->all();
        if (!array_key_exists("message", $data)) {
            return;
        }

        $chat = Chat::query()->where('chat_id', '=', $data["message"]["chat"]["id"])->first();
        if (!$chat) {
            return;
        }


        $type = $data["message"]["chat"]["type"];
        if ($type == TelegramBotEnum::GROUP_BOT->value) {
            try {
                $group->analyze($data);
            } catch (Exception $e) {
                $exceptionTelegram->send($e, $data["message"]["chat"], $data["message"]["message_id"]);
            }
        }


//        if ($type == TelegramBotEnum::CHAT_BOT->value) {
//            // если это чат
//        }

        //        $message = new MessageTelegram();
        //        $message->main = json_encode($data);
        //        $message->save();

    }

}
