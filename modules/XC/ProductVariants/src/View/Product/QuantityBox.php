<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Quantity box
 * @Extender\Mixin
 */
class QuantityBox extends \XLite\View\Product\QuantityBox
{
    /**
     * Return additional validate
     *
     * @return string
     */
    protected function getAdditionalValidate()
    {
        $maxValue = $this->getParam(self::PARAM_MAX_VALUE);
        $nonDefaultAmount = isset($maxValue)
            || (
                $this->getOrderItem()
                && $this->getOrderItem()->getVariant()
                && !$this->getOrderItem()->getVariant()->getDefaultAmount()
            );

        return $nonDefaultAmount || $this->getProduct()->getInventoryEnabled() ? ',max[' . $this->getMaxQuantity() . ']' : '';
    }

    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        return $this->getOrderItem() && $this->getOrderItem()->getVariant()
            ? $this->getOrderItem()->getVariant()->getAvailableAmount()
            : parent::getMaxQuantity();
    }
}
