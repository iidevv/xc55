<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Couriers\Command;

use XLite\Core\Database;

/**
 * Abstract class command
 */
abstract class ACommand
{
    /**
     * Flush courier item(s)
     *
     * @return void
     * @throws \Exception
     */
    protected function do(): void
    {
        Database::getEM()->flush();
    }
}
