<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\Handler;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use XCart\Messenger\Message\Dummy;

class DummyHandler implements MessageHandlerInterface
{
    public function __invoke(Dummy $message)
    {
        echo 'Dummy!';
    }
}
