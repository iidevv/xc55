<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\FormField;

/**
 * Taxable base selector
 */
class TaxableBase extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = [];

        $options[\CDev\SalesTax\Model\Tax\Rate::TAXBASE_SUBTOTAL_SHIPPING]
            = static::t('Subtotal + Shipping cost');
        $options[\CDev\SalesTax\Model\Tax\Rate::TAXBASE_DISCOUNTED_SUBTOTAL_SHIPPING]
            = static::t('Discounted subtotal + Shipping cost');
        $options[\CDev\SalesTax\Model\Tax\Rate::TAXBASE_SUBTOTAL]
            = static::t('Subtotal');
        $options[\CDev\SalesTax\Model\Tax\Rate::TAXBASE_DISCOUNTED_SUBTOTAL]
            = static::t('Discounted subtotal');

        $options['P'] = static::t('Individual settings for every rate');

        return $options;
    }
}
