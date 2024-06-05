<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class represents an order item model
 * @Extender\Mixin
 */
abstract class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Canada Post parcel items (reference to the Canada Post parcel item model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\CanadaPost\Model\Order\Parcel\Item", mappedBy="orderItem")
     */
    protected $capostParcelItems;

    /**
     * Canada Post return items (reference to the Canada Post return item model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\CanadaPost\Model\ProductsReturn\Item", mappedBy="orderItem", cascade={"all"})
     */
    protected $capostReturnItems;

    /**
     * Canada Post return items (reference to the Canada Post return item model)
     *
     * @var integer
     */
    protected $copyOfAmount;

    /**
     * Add a Canada Post parcel item
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $newItem Parcel's item model
     *
     * @return void
     */
    public function addCapostParcelItem(\XC\CanadaPost\Model\Order\Parcel\Item $newItem)
    {
        $newItem->setOrderItem($this);

        $this->addCapostParcelItems($newItem);
    }

    /**
     * Add a Canada Post return item
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $newItem Retrun's item model
     *
     * @return void
     */
    public function addCapostReturnItem(\XC\CanadaPost\Model\ProductsReturn\Item $newItem)
    {
        $newItem->setOrderItem($this);

        $this->addCapostReturnItems($newItem);
    }

    /**
     * Add capostParcelItems
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $capostParcelItems
     * @return OrderItem
     */
    public function addCapostParcelItems(\XC\CanadaPost\Model\Order\Parcel\Item $capostParcelItems)
    {
        $this->capostParcelItems[] = $capostParcelItems;
        return $this;
    }

    /**
     * Get capostParcelItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCapostParcelItems()
    {
        return $this->capostParcelItems;
    }

    /**
     * Add capostReturnItems
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $capostReturnItems
     * @return OrderItem
     */
    public function addCapostReturnItems(\XC\CanadaPost\Model\ProductsReturn\Item $capostReturnItems)
    {
        $this->capostReturnItems[] = $capostReturnItems;
        return $this;
    }

    /**
     * Get capostReturnItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCapostReturnItems()
    {
        return $this->capostReturnItems;
    }

    /**
     * Set copy of amount
     */
    public function setCopyOfAmount()
    {
        $this->copyOfAmount = $this->getAmount();
    }

    /**
     * Return copy of amount
     *
     * @return int|null
     */
    public function getCopyOfAmount()
    {
        return $this->copyOfAmount;
    }
}
