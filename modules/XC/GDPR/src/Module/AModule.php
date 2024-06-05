<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AModule extends \XLite\Module\AModule
{
    /**
     * @return boolean
     */
    public static function hasGdprRelatedActivity()
    {
        return false;
    }
}
