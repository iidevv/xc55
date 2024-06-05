<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariantOrderItem extends \XLite\Model\OrderItem
{
    /**
     * Get price
     *
     * @return float
     */
    public function getClearPrice()
    {
        $this->setWholesaleValues();

        return parent::getClearPrice();
    }
}
