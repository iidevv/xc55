<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View;

use XCart\Extender\Mapping\Extender;

/**
 * Tabber
 * @Extender\Mixin
 */
class Tabber extends \XLite\View\Tabber
{
    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        $result = parent::isTabsNavigationVisible();

        if (
            $result
            && $this->getTarget() === 'product_variant'
        ) {
            $result = 2 < count($this->getTabberPages());
        }

        return $result;
    }
}
