<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\View\FormField\Select;

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
            SpecialOffer::BXGY_DISCOUNT_TYPE_PERCENT => static::t('Percent discount (%)'),
            SpecialOffer::BXGY_DISCOUNT_TYPE_FIXED   => static::t('Fixed-sum discount (X)', ['currency' => \XLite::getInstance()->getCurrency()->getSymbol()]),
        ];
    }
}
