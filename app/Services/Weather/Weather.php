<?php

namespace App\Services\Weather;

use Illuminate\Support\Facades\Http;

class Weather
{

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $geo
     * @return \Illuminate\Http\Client\Response
     */
    public function search(string $geo)
    {
        return Http::get($this->config["url"], [
            'q' => $geo,
            'type' => 'like',
            'lang' => 'ru',
            "APPID" => $this->config["key"],
        ]);

    }
}
