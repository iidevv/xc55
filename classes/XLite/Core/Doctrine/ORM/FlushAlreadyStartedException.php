<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\ORM;

class FlushAlreadyStartedException extends \Exception
{
    /**
     * @return FlushAlreadyStartedException
     */
    public static function flushAlreadyStarted()
    {
        return new self('Flush already started. It is not allowed to call flush inside lifecycle callbacks directly, use afterFlushCallback instead.');
    }
}
