<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\FormField;

/**
 * Freight shipping calculation mode selector
 */
class FreightMode extends \XLite\View\FormField\Select\Regular
{
    /**
     * Values
     */
    public const FREIGHT_ONLY = 'F'; // Use freight fixed fee only
    public const FREIGHT_ADD  = 'B'; // Add freight fixed fee to a base shipping rate

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::FREIGHT_ONLY => static::t('Shipping freight only'),
            static::FREIGHT_ADD  => static::t('Shipping freight + regular shipping rate'),
        ];
    }
}
