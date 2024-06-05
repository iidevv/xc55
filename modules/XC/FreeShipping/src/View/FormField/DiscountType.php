<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\FormField;

use XCart\Extender\Mapping\Extender;

/**
 * Discount type selector
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class DiscountType extends \CDev\Coupons\View\FormField\DiscountType
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();
        $options[\CDev\Coupons\Model\Coupon::TYPE_FREESHIP] = static::t('Free shipping');

        return $options;
    }
}
