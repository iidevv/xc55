<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Something customer can put into his cart
 *
 * @Extender\Mixin
 */
abstract class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Item SKU
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true, options={ "default": "0" })
     */
    protected $freeGift = false;


    public function getFreeGift()
    {
        return (bool)$this->freeGift;
    }

    public function setFreeGift($freeGift)
    {
        $this->freeGift = $freeGift;
        return $this;
    }

    public function getKey()
    {
        return parent::getKey() . ($this->getFreeGift() ? '-1' : '-0');
    }

    public function getClearPrice()
    {
        return $this->getFreeGift() ? 0 : parent::getClearPrice();
    }

    public function canChangeAmount()
    {
        return !$this->getFreeGift()
            && parent::canChangeAmount();
    }
}
