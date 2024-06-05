<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout success controller
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    /**
     * @inheritdoc
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        $orders = \XLite\Core\Session::getInstance()->segmentCompletedOrders;
        if (!is_array($orders)) {
            $orders = [];
        }
        if (
            !\XLite\Core\Request::getInstance()->isAJAX()
            && in_array($this->getTarget(), ['checkout_success', 'checkoutSuccess'])
            && $this->getOrder()
            && !in_array($this->getOrder()->getOrderId(), $orders)
        ) {
            \QSL\Segment\Core\Mediator::getInstance()->doCompleteOrder($this->getOrder());
            $orders[] = $this->getOrder()->getOrderId();
            \XLite\Core\Session::getInstance()->segmentCompletedOrders = $orders;
        }
    }
}
