<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Address extends \XLite\Model\Address
{
    /**
     * Check if addresses are equal
     *
     * @param \XLite\Model\Address $address
     *
     * @return boolean
     */
    public function equals($address)
    {
        return $this->toArray() === $address->toArray();
    }
}
