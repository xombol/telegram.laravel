<?php

namespace App\Services\Telegram\Group;

use App\Services\Telegram\Group\AnalyzeData;

class Group
{
    private AnalyzeData $analyzeData;

    public function __construct(AnalyzeData $analyzeData)
    {
        $this->analyzeData = $analyzeData;
    }


    /***
     * AnalyzeData data Group
     * @return void
     */
    public function analyze(array $data): void
    {
        $message = $data["message"];
        $this->analyzeData->processing($message);
    }

}
