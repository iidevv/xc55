<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Sections;

use XCart\Extender\Mapping\Extender;

/**
 * Widget class of Address section of the fastlane checkout
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\FastLaneCheckout")
 */
class Address extends \XC\FastLaneCheckout\View\Sections\Address
{
    /**
     * @return string
     */
    protected function getBillingFormTitle()
    {
        return static::t('Billing');
    }

    /**
     * @return string
     */
    protected function getShippingFormTitle()
    {
        return static::t('Shipping');
    }
}
