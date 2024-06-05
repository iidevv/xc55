<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\FormField;

/**
 * Address type selector
 */
class AddressType extends \XLite\View\FormField\Select\Regular
{
    public const ADDRESS_TYPE_BILLING  = 'billing';
    public const ADDRESS_TYPE_SHIPPING = 'shipping';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::ADDRESS_TYPE_BILLING  => static::t('Billing address'),
            static::ADDRESS_TYPE_SHIPPING => static::t('Shipping address'),
        ];
    }
}
