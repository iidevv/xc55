<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Module\CDev\Paypal\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Class Checkout
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Paypal")
 * @Extender\After ("XC\NotFinishedOrders")
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Return true if specified processor allows to create NFO on place order action
     *
     * @return boolean
     */
    protected function isAllowedPlaceOrderNFO()
    {
        $cart = $this->getCart();
        $method = $cart->getPaymentMethod();

        return parent::isAllowedPlaceOrderNFO()
            && $method
            && !$cart->isExpressCheckout($method);
    }

    /**
     * @param $note
     */
    protected function setOrderNote($note)
    {
        parent::setOrderNote($note);

        if ($nfo = $this->getCart()->getNotFinishedOrder()) {
            $nfo->setNotes($this->getCart()->getNotes());
        }
    }
}
