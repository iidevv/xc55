<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAmountForFreeShipping\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate OrderItem model
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    public function isShipForFreeByCategoryAmount(): bool
    {
        $cat = $this->getOrder()?->getCalculatedFreeShippingCategories($this->getProduct()->getCategoryId());

        return (
            $cat !== null
            && ($cat['free_shipping_amount'] > 0)
            && ($cat['total'] >= $cat['free_shipping_amount'])
        );
    }

    public function isShipForFree()
    {
        return $this->isShipForFreeByCategoryAmount()
            || parent::isShipForFree();
    }
}
