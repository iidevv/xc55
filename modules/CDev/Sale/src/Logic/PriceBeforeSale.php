<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic;

/**
 * Price before sale
 */
class PriceBeforeSale extends \XLite\Logic\Price
{
    /**
     * Define modifiers
     *
     * @return array
     */
    protected function defineModifiers()
    {
        $modifiers = parent::defineModifiers();
        foreach ($modifiers as $i => $modifier) {
            if (strpos($modifier->getClass(), 'CDev\Sale\\') === 0) {
                unset($modifiers[$i]);
            }
        }

        return $modifiers;
    }
}
