<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory;

use XLite\Logger;

class LoggerFactory
{
    public static function logger()
    {
        return Logger::getLogger('SkinActSkuVault');
    }
}
