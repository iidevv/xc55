<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\Model;

use XCart\Extender\Mapping\Extender;

/**
 * OrderItem class extension
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Get discountedSubtotal including VAT
     *
     * @return float
     */
    public function getDiscountedSubtotalIncludingVAT()
    {
        $discountedSubtotal = $this->getDiscountedSubtotal();

        if (
            method_exists($this, 'isDisplayPricesIncludingVAT')
            && $this->isDisplayPricesIncludingVAT()
            && $discountedSubtotal < $this->getSubtotal()
        ) {
            $total = $this->getSubtotal();
            $discount = $this->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT);

            return $total - $discount;
        }

        return $discountedSubtotal;
    }
}
