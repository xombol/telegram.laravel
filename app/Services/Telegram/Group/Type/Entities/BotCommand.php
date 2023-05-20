<?php

namespace App\Services\Telegram\Group\Type\Entities;

class BotCommand
{
    /**
     * Проверка на совпадение
     *
     * @return void
     */
    public function filter(array $data)
    {
        if ($this->search('/weather', $data)) {
            // $this->ent_weather();
        }
    }

    /**
     * поиск в строке или проверка срау строки
     *
     * @param string $text
     * @return bool
     */
    private function search(string $text, $data): bool
    {
        return (($data["text"] == $text) || (str_contains($data["text"], $text)));
    }
}
