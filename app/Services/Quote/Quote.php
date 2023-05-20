<?php

namespace App\Services\Quote;

use Illuminate\Support\Facades\Http;

class Quote
{
    private const URL = 'http://api.forismatic.com/api/1.0/?';

    public function random()
    {
        $res = Http::get(self::URL, ['method' => 'getQuote', 'format' => 'text']);
        return $res;

    }
}
