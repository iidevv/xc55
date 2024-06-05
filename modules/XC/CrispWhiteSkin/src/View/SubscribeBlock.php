<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * SubscribeBlock decorator
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\NewsletterSubscriptions")
 */
abstract class SubscribeBlock extends \XC\NewsletterSubscriptions\View\SubscribeBlock
{
    /**
     * Check if form input is field only
     *
     * @return boolean
     */
    public function isFieldOnly()
    {
        return false;
    }
}
