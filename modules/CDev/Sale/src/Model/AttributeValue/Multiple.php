<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model\AttributeValue;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Multiple extends \XLite\Model\AttributeValue\Multiple
{
    /**
     * Get price modifier base value
     *
     * @return float
     */
    protected function getModifierBasePrice()
    {
        return $this->getProduct()->getParticipateSale() ? $this->getProduct()->getSalePriceValue() : parent::getModifierBasePrice();
    }
}
