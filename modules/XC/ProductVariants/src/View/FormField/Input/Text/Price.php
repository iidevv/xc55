<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\FormField\Input\Text;

/**
 * Price
 */
class Price extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Get default E
     *
     * @return integer
     */
    protected static function getDefaultE()
    {
        return \XLite::getInstance()->getCurrency()->getE();
    }

    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitizeFloat($value)
    {
        return $value !== '' ? parent::sanitizeFloat($value) : $value;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();

        $attributes['value'] = $attributes['value'] !== ''
            ? parent::sanitizeFloat($attributes['value'])
            : '';

        return $attributes;
    }
}
