<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\MyWishlist\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 */
abstract class AWishlist extends \QSL\MyWishlist\View\ItemsList\Product\Customer\AWishlist
{
    /**
     * @return string
     */
    protected function getDisplayMode()
    {
        return $this->isMobileDevice() ? parent::getDisplayMode() : self::DISPLAY_MODE_LIST;
    }

    /**
     * @return bool
     */
    protected function isDisplayModeSelectorVisible()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/MyWishlist/items_list/product/close_button.js';

        return $list;
    }
}
