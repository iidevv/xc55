<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XC\NotFinishedOrders\Main;

/**
 * Checkout controller
 * @Extender\Mixin
 */
abstract class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Order placement is success
     *
     * @param boolean $fullProcess Full process or not OPTIONAL
     *
     * @return void
     */
    public function processSucceed($fullProcess = true)
    {
        $cart = $this->getCart();

        if ($cart && $cart->getNotFinishedOrder()) {
            $this->removeNotFinishedOrder($cart);
            \XLite\Core\Request::getInstance()->should_allow_long_calculations = true;
        }

        parent::processSucceed($fullProcess);
    }

    /**
     * Assign additional data to cart during doPayment process
     *
     * @param $cart
     */
    protected function assignAdditionalDataToCart(\XLite\Model\Order $cart)
    {
        parent::assignAdditionalDataToCart($cart);

        if ($nfo = $cart->getNotFinishedOrder()) {
            $nfo->setNotes($cart->getNotes());
        }
    }

    /**
     * Does the payment and order status handling
     */
    protected function doPayment()
    {
        $cart = $this->getCart();

        if (
            Main::isCreateOnPlaceOrder()
                && $this->isAllowedPlaceOrderNFO()
        ) {
            // If NFO should be created on 'Place order' action and current payment processor is not Offline,
            // then create NFO and reassign transaction on new order (cart)

            /** @var \XC\NotFinishedOrders\Model\Cart $cart */
            $cart->processNotFinishedOrder(true);
        }

        parent::doPayment();
    }

    /**
     * Return true if specified processor allows to create NFO on place order action
     *
     * @return boolean
     */
    protected function isAllowedPlaceOrderNFO()
    {
        $transaction = $this->getCart()->getFirstOpenPaymentTransaction();
        $processor = $transaction ? $transaction->getPaymentMethod()->getProcessor() : null;
        return $processor && !($processor instanceof \XLite\Model\Payment\Processor\Offline);
    }

    /**
     * Remove not finished order
     *
     * @param \XLite\Model\Order $cart Not finished order to remove
     *
     * @return void
     */
    protected function removeNotFinishedOrder($cart)
    {
        $cart->removeNotFinishedOrder(true);
    }

    /**
     * Process cart profile
     *
     * @param boolean $doCloneProfile Clone profile flag
     *
     * @return boolean
     */
    protected function processCartProfile($doCloneProfile)
    {
        $cart = $this->getCart();

        if ($cart && $cart->isNotFinishedOrder()) {
            $doCloneProfile = false;
        }

        return parent::processCartProfile($doCloneProfile);
    }
}
