<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Order return
     *
     * @var \QSL\Returns\Model\OrderReturn
     *
     * @ORM\OneToOne  (targetEntity="QSL\Returns\Model\OrderReturn", mappedBy="order", cascade={"all"})
     */
    protected $orderReturn;

    /*
     * Get date of return creating (for 'order details' page, 'order return' section)
     */
    public function getOrderReturnDate()
    {
        $return = $this->getOrderReturn();

        return $return
            ? $return->getDate()
            : 0;
    }

    /*
     * Get return status (for 'order details' page, 'order return' section)
     */
    public function getOrderReturnStatus()
    {
        $return = $this->getOrderReturn();

        return $return
            ? $return->getStatusName($return->getStatus())
            : '';
    }

    /*
     * Get return reason (for 'order details' page, 'order return' section)
     */
    public function getOrderReturnReason()
    {
        $return = $this->getOrderReturn();

        return $return && $return->getReason()
            ? $return->getReason()->getReasonName()
            : static::t('Other');
    }

    /*
     * Get return action (for 'order details' page, 'order return' section)
     */
    public function getOrderReturnAction()
    {
        $return = $this->getOrderReturn();

        return $return && $return->getAction()
            ? $return->getAction()->getActionName()
            : static::t('Other');
    }

    /*
     * Get return comment (for 'order details' page, 'order return' section)
     */
    public function getOrderReturnComment()
    {
        $return = $this->getOrderReturn();

        return $return
            ? $return->getComment()
            : '';
    }

    /*
     * Get return items (for 'order details' page, 'order return' section)
     */
    public function getOrderReturnItems()
    {
        $return = $this->getOrderReturn();

        return $return && $return->getItems()
            ? $return->getItems()
            : [];
    }

    /**
     * Get order item by ID
     *
     * @param integer $id Order item ID
     *
     * @return \XLite\Model\OrderItem
     */
    public function getOrderItemById($id)
    {
        foreach ($this->getItems() as $item) {
            if ($item->getItemId() == $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Set orderReturn
     *
     * @param \QSL\Returns\Model\OrderReturn $orderReturn
     *
     * @return Order
     */
    public function setOrderReturn(\QSL\Returns\Model\OrderReturn $orderReturn = null)
    {
        $this->orderReturn = $orderReturn;

        return $this;
    }

    /**
     * Get orderReturn
     *
     * @return \QSL\Returns\Model\OrderReturn
     */
    public function getOrderReturn()
    {
        return $this->orderReturn;
    }

    /**
     * Checks if the order has a submitted return request.
     *
     * @return boolean
     */
    public function hasOrderReturn()
    {
        return $this->orderReturn instanceof \QSL\Returns\Model\OrderReturn;
    }

    /**
     * Checks if the customer can claim a return.
     *
     * @return boolean
     */
    public function canReturnBeClaimed()
    {
        return !$this->hasOrderReturn()
            && $this->isShippingStatusAllowedForReturn();
    }

    /**
     * Checks if the order has a fulfillment status that allows a return
     * to be requested.
     *
     * @return boolean
     */
    public function isShippingStatusAllowedForReturn()
    {
        $status = $this->getShippingStatus();

        return $status && $status->isReturnRequestAllowed();
    }
}
