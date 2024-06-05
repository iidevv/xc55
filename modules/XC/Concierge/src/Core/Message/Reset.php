<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Core\Message;

use XC\Concierge\Core\AMessage;

class Reset extends AMessage
{
    public function getType()
    {
        return static::TYPE_RESET;
    }

    public function getArguments()
    {
        return [];
    }
}
