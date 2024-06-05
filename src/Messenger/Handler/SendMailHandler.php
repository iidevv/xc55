<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\Handler;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use XCart\Messenger\Message\SendMail;

class SendMailHandler implements MessageHandlerInterface
{
    public function __invoke(SendMail $message): void
    {
        $args = $message->getArgs();
        $messageClass = $message->getMailClass();

        if ($messageClass::isEnabled()) {
            (new $messageClass(...$args))->send();
        }
    }
}
