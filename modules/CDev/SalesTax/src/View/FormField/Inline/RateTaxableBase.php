<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\FormField\Inline;

/**
 * Rate taxable base
 */
class RateTaxableBase extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'CDev\SalesTax\View\FormField\TaxableBase';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $list = parent::getFieldParams($field);

        $list['options'] = $this->getOptions();

        return $list;
    }

    /**
     * Get list of options for widget
     *
     * @return array
     */
    protected function getOptions()
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

        return $options;
    }
}
