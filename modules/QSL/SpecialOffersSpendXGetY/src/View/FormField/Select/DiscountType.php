<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersSpendXGetY\View\FormField\Select;

use QSL\SpecialOffersBase\Model\SpecialOffer;

/**
 * Payment method fee type selector.
 */
class DiscountType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            SpecialOffer::SXGY_DISCOUNT_TYPE_PERCENT => static::t('Percent discount (%)'),
            // TODO: change "fixed-sum" to "fixed-amount" when Special Offers Base 5.2.4 is released
            SpecialOffer::SXGY_DISCOUNT_TYPE_FIXED   => static::t('Fixed-sum discount (X)', ['currency' => \XLite::getInstance()->getCurrency()->getSymbol()]),
        ];
    }
}
