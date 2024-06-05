<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\FormField\Inline\Input\Text;

/**
 * Price
 */
class Price extends \XC\ProductVariants\View\FormField\Inline\Input\Text\DefaultValue
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XC\ProductVariants\View\FormField\Input\Text\Price';
    }

    /**
     * @inheritdoc
     */
    protected function getPlaceholder()
    {
        return $this->getProduct()
            ? $this->formatPrice($this->getProduct()->getPrice())
            : parent::getPlaceholder();
    }
}
