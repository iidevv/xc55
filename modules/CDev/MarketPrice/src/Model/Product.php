<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\MarketPrice\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Product market price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $marketPrice = 0.0000;

    /**
     * Set marketPrice
     *
     * @param float $marketPrice
     * @return Product
     */
    public function setMarketPrice($marketPrice)
    {
        $this->marketPrice = Converter::toUnsigned32BitFloat($marketPrice);
        return $this;
    }

    /**
     * Get marketPrice
     *
     * @return float
     */
    public function getMarketPrice()
    {
        return $this->marketPrice;
    }

    /**
     * @return float
     */
    public function getNetMarketPrice()
    {
        return $this->getMarketPrice();
    }

    /**
     * @return float
     */
    public function getDisplayMarketPrice()
    {
        return $this->getMarketPrice();
    }
}
