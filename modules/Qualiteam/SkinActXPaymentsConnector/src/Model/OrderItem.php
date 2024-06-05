<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * Fake order item for zero auth's and recharges from X-Paymemts.
 * Something customer can not put into his cart
 *
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Flag for zero auth and recharges
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $xpcFakeItem = false;

    /**
     * Is this item a fake one for zero auth and recharges
     *
     * @return boolean
     */
    public function isXpcFakeItem()
    {
        return $this->getXpcFakeItem();
    }

    /**
     * Check if item is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->isXpcFakeItem()
            || parent::isValid();
    }

    /**
     * Deleted Item flag
     *
     * @return boolean
     */
    public function isDeleted()
    {
        $result = parent::isDeleted();

        if ($this->isXpcFakeItem()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Returns deleted product for fake items
     *
     * @return Product
     */
    public function getProduct()
    {
        if ($this->isXpcFakeItem()) {
            return $this->getDeletedProduct();
        } else {
            return parent::getProduct();
        }
    }

    /**
     * Returns deleted product for fake items
     *
     * @return Product
     */
    public function getObject()
    {
        if ($this->isXpcFakeItem()) {
            return $this->getDeletedProduct();
        } else {
            return parent::getObject();
        }
    }

    /**
     * Check if the item is valid to clone through the Re-order functionality
     *
     * @return boolean
     */
    public function isValidToClone()
    {
        if ($this->isXpcFakeItem()) {

            $result = false;

        } else {

            $result = parent::isValidToClone();
        }

        return $result;
    }

    /**
     * Set xpcFakeItem
     *
     * @param boolean $xpcFakeItem
     * @return OrderItem
     */
    public function setXpcFakeItem($xpcFakeItem)
    {
        $this->xpcFakeItem = $xpcFakeItem;
        return $this;
    }

    /**
     * Get xpcFakeItem
     *
     * @return boolean
     */
    public function getXpcFakeItem()
    {
        return $this->xpcFakeItem;
    }

    /**
    * Get item clear price. This value is used as a base item price for calculation of netPrice
    *
    * @return float
    */
    public function getClearPrice()
    {
        if ($this->isXpcFakeItem()) {
            return parent::getPrice();
        } else {
            return parent::getClearPrice();
        }
    }
}
