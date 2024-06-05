<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

/**
 * QuantityBox
 */
class CartQuantityBox extends \XLite\View\Product\QuantityBox
{
    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        return $this->getProduct()->getAvailableAmount();
    }
}
