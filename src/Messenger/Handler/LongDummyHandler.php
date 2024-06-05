<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\Handler;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use XCart\Messenger\Message\LongDummy;

class LongDummyHandler implements MessageHandlerInterface
{
    public function __invoke(LongDummy $message)
    {
        $timeToWork = 20 * 60;
        $sleep = 5;

        $iteration = $timeToWork / $sleep;

        for ($i = 0; $i < $iteration; $i++) {
            echo "Long dummy $1!";
            usleep($sleep * 1000000);
        }
    }
}
