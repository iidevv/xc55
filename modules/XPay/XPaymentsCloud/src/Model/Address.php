<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Address
 *
 * @Extender\Mixin
 */
abstract class Address extends \XLite\Model\Address implements \XLite\Base\IDecorator
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
