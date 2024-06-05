<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Class Cart
 *
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants", "XC\FacebookMarketing"})
 */
class CartVariants extends \XLite\Controller\Customer\Cart
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return integer
     */
    protected function getItemId($item)
    {
        if ($item->getVariant()) {
            return $item->getVariant()->getSku() ?: $item->getVariant()->getVariantId();
        }

        return parent::getItemId($item);
    }
}
