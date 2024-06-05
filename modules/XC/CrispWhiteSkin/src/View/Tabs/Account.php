<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to user profile section
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    /**
     * Returns the default widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs.twig';
    }
}
