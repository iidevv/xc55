<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Converter;

/**
 * Decorate product model
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    //TODO freeShipping, freeShip, ShipForFree - Rename to
    // nonShippable?, ExcludeFromShipping, FreeShipping

    /**
     * Is product excluded from shipping calculation
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $freeShip = false;

    /**
     * Is free shipping available for the product
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $shipForFree = false;

    /**
     * Shipping freight fixed fee
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $freightFixedFee = 0;

    /**
     * Set freeShip
     *
     * @param boolean $freeShip
     * @return Product
     */
    public function setFreeShip($freeShip)
    {
        $this->freeShip = (bool) $freeShip;
        return $this;
    }

    /**
     * Get freeShip
     *
     * @return boolean
     */
    public function getFreeShip()
    {
        return $this->freeShip;
    }

    /**
     * Return ShipForFree
     *
     * @return bool
     */
    public function isShipForFree()
    {
        return $this->shipForFree;
    }

    /**
     * Set ShipForFree
     *
     * @param bool $shipForFree
     *
     * @return $this
     */
    public function setShipForFree($shipForFree)
    {
        $this->shipForFree = $shipForFree;
        return $this;
    }

    /**
     * Set freightFixedFee
     *
     * @param float $freightFixedFee
     * @return Product
     */
    public function setFreightFixedFee($freightFixedFee)
    {
        $this->freightFixedFee = Converter::toUnsigned32BitFloat($freightFixedFee);
        return $this;
    }

    /**
     * Get freightFixedFee
     *
     * @return float
     */
    public function getFreightFixedFee()
    {
        return $this->freightFixedFee;
    }
}
