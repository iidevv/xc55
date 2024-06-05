<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Helpers;

use XLite\Model\Address;

class BillingAddress
{
    /**
     * @var \XLite\Model\Address|null
     */
    protected ?Address $address;

    /**
     * @param \XLite\Model\Address|null $address
     */
    public function __construct(?Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getBillingAddressStreet(): string
    {
        return $this->address ? $this->address->getStreet() : '';
    }

    /**
     * @return string
     */
    public function getBillingAddressCity(): string
    {
        return $this->address ? $this->address->getCity() : '';
    }

    /**
     * @return string
     */
    public function getBillingAddressState(): string
    {
        return $this->address ? $this->address->getState()?->getState() : '';
    }

    /**
     * @return string
     */
    public function getBillingAddressZipcode(): string
    {
        return $this->address ? $this->address->getZipcode() : '';
    }

    /**
     * @return string
     */
    public function getBillingAddressProvinceCode(): string
    {
        return $this->address && $this->address->getState() ? $this->address->getState()->getCode() : '';
    }

    /**
     * @return string
     */
    public function getBillingAddressCountryCode(): string
    {
        return $this->address ? $this->address->getCountryCode() : '';
    }

    /**
     * @return string
     */
    public function getBillingAddressPhone(): string
    {
        $address = $this->address;
        $phoneField = $address?->getFieldValue('phone');
        return $phoneField ? $phoneField->getValue() : '';
    }
}