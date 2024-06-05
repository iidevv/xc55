<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\Message;

class SendMail
{
    private $mailClass;

    private $args;

    public function __construct(string $mailClass = null, array $args = [])
    {
        $this->mailClass = $mailClass;
        $this->args      = $args;
    }

    public function getMailClass(): string
    {
        return $this->mailClass;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}
