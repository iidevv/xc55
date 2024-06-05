<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    /**
     * Flag if the favicon is displayed in the customer area
     *
     * If the custom favicon is defined then the favicon will be displayed
     *
     * @return boolean
     */
    protected function displayFavicon()
    {
        return parent::displayFavicon() || (bool) \XLite\Core\Config::getInstance()->CDev->SimpleCMS->favicon;
    }
}
