<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping rate model
 * @Extender\Mixin
 */
class Rate extends \XLite\Model\Shipping\Rate
{
    /**
     * Base rate value
     *
     * @var float
     */
    protected $freightRate = 0;

    /**
     * Return FreightRate
     *
     * @return float
     */
    public function getFreightRate()
    {
        return $this->freightRate;
    }

    /**
     * Set FreightRate
     *
     * @param float $freightRate
     *
     * @return $this
     */
    public function setFreightRate($freightRate)
    {
        $this->freightRate = $freightRate;
        return $this;
    }

    /**
     * getTotalRate
     *
     * @return float
     */
    public function getTotalRate()
    {
        return parent::getTotalRate() + $this->getFreightRate();
    }
}
