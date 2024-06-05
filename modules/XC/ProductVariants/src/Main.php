<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check if product price in list should be displayed as range
     *
     * @return bool
     */
    public static function isDisplayPriceAsRange()
    {
        return \XLite\Core\Config::getInstance()->XC->ProductVariants->price_in_list
            === \XC\ProductVariants\View\FormField\Select\PriceInList::DISPLAY_RANGE;
    }
}
