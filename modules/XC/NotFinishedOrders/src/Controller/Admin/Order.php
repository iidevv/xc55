<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents an order
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Return true if order can be edited
     *
     * @return boolean
     */
    public function isOrderEditable()
    {
        return parent::isOrderEditable() && !($this->getOrder() instanceof \XLite\Model\Cart);
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        $order = parent::getOrder();

        if ($order === null && \XLite\Core\Request::getInstance()->order_id) {
            $order = \XLite\Core\Database::getRepo('XLite\Model\Cart')
                ->find((int) \XLite\Core\Request::getInstance()->order_id);

            $this->order = $order && $order->isNotFinishedOrder()
                ? $order
                : null;
        }

        return $this->order;
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $order = $this->getOrder();

        if ($order->isNotFinishedOrder()) {
            $order->closeNotFinishedOrder();
        }

        parent::doActionUpdate();
    }
}
