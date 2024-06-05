<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Quantity extends \XLite\View\Product\Details\Customer\Quantity
{
    /**
     * Define the CSS classes
     *
     * @return string
     */
    protected function getCSSClass()
    {
        return parent::getCSSClass() . ($this->hasWholesalePrice() ? ' wholesale-price-defined' : '');
    }

    /**
     * Check if the product has wholesale price
     *
     * @return boolean
     */
    protected function hasWholesalePrice()
    {
        return \XLite\Core\Database::getRepo('CDev\Wholesale\Model\WholesalePrice')->hasWholesalePrice(
            $this->getProduct()
        );
    }
}
