<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\MultiVendor\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Orders list menu item
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class OrdersList extends \XC\CrispWhiteSkin\View\Menu\Customer\OrdersList
{
    /**
     * @return string
     */
    protected function getDefaultCaption()
    {
        return \XLite\Core\Auth::getInstance()->isVendor()
            ? static::t('My purchases')
            : parent::getDefaultCaption();
    }
}
