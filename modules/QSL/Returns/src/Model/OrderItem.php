<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order item
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Order return items
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\Returns\Model\ReturnItem", mappedBy="orderItem", cascade={"merge","detach"})
     */
    protected $returnItems;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->returnItems = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get return item by orderID
     *
     * @param integer $orderId Order ID
     *
     * @return \QSL\Returns\Model\ReturnItem
     */
    public function getReturnItemByOrder($orderId)
    {
        foreach ($this->getReturnItems() as $item) {
            if (
                $item->getOrderReturn()
                && $item->getOrderReturn()->getOrder()
                && $item->getOrderReturn()->getOrder()->getOrderId() === $orderId
            ) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Add returnItems
     *
     * @param \QSL\Returns\Model\ReturnItem $returnItems
     * @return OrderItem
     */
    public function addReturnItems(\QSL\Returns\Model\ReturnItem $returnItems)
    {
        $this->returnItems[] = $returnItems;
        return $this;
    }

    /**
     * Get returnItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReturnItems()
    {
        return $this->returnItems;
    }

    public function deleteReturnItems()
    {
        $this->returnItems->clear();
    }
}
